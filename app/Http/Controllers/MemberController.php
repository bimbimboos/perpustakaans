<?php

namespace App\Http\Controllers;

use App\Models\Members;
use App\Models\User;
use App\Mail\MemberVerifiedNotification;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    /**
     * Constructor - Apply role-based middleware
     */
    public function __construct()
    {
        // Semua route member harus authenticated
        $this->middleware('auth');

        // Hanya admin & petugas yang bisa akses member management
        $this->middleware('role:admin,petugas')->except(['myProfile']);
    }

    /**
     * Helper: Check if user is admin or petugas
     */
    private function isAdminOrPetugas(): bool
    {
        return in_array(strtolower(auth()->user()->role), ['admin', 'petugas']);
    }

    /**
     * Display listing of members (Admin & Petugas only)
     */
    public function index(Request $request)
    {
        $query = Members::query()->with(['user', 'verifiedBy']);

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('no_telp', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Verified filter
        if ($request->has('verified')) {
            if ($request->verified === '1') {
                $query->whereNotNull('admin_verified_at');
            } else {
                $query->whereNull('admin_verified_at');
            }
        }

        $members = $query->latest()->paginate(20);

        return view('members.index', compact('members'));
    }

    /**
     * Store new member (Admin & Petugas only) - MODAL FORM
     */
    public function store(StoreMemberRequest $request)
    {
        $validated = $request->validated();

        // Check duplicate KTP
        $ktpHash = hash('sha256', $request->ktp_number);
        if (Members::where('ktp_hash', $ktpHash)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor identitas sudah terdaftar!'
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Generate kode verifikasi
            $verificationCode = Members::generateVerificationCode();
            $tahunPembuatan = now()->year;

            // Buat data member (TANPA user, karena member tidak login)
            $member = new Members();
            $member->name = $request->name;
            $member->tempat_lahir = $request->tempat_lahir;
            $member->tanggal_lahir = $request->tanggal_lahir;
            $member->email = $request->email;
            $member->agama = $request->agama;
            $member->alamat = $request->alamat;
            $member->institusi = $request->institusi;
            $member->alamat_institusi = $request->alamat_institusi;
            $member->jenjang_pendidikan = $request->jenjang_pendidikan;
            $member->no_telp = $request->no_telp;
            $member->no_hp_ortu = $request->no_hp_ortu;
            $member->role = 'member';

            // Langsung verified
            $member->status = 'verified';
            $member->verification_code = $verificationCode;
            $member->tahun_pembuatan = $tahunPembuatan;
            $member->admin_verified_at = now();
            $member->verified_by = auth()->user()->id_user;

            $member->setKtpNumber($request->ktp_number);
            $member->save();

            $memberPath = "members/{$member->id_member}";

            // Upload KTP/Kartu Pelajar
            if ($request->hasFile('ktp_photo')) {
                $file = $request->file('ktp_photo');
                $filename = 'ktp_' . Str::random(16) . '.' . $file->getClientOriginalExtension();
                $member->ktp_photo_path = $file->storeAs($memberPath, $filename, 'private');
                $member->ktp_verified_at = now();
            }

            // Upload Pas Foto
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = 'photo_' . Str::random(16) . '.' . $file->getClientOriginalExtension();
                $member->photo_path = $file->storeAs($memberPath, $filename, 'private');
            }

            $member->save();

            // No Anggota (format: TAHUN-ID dengan padding 4 digit)
            $noAnggota = $tahunPembuatan . '-' . str_pad($member->id_member, 4, '0', STR_PAD_LEFT);

            \Log::info('New member created', [
                'member_id' => $member->id_member,
                'no_anggota' => $noAnggota,
                'name' => $member->name,
                'verification_code' => $verificationCode,
                'created_by' => auth()->user()->id_user,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Member berhasil ditambahkan!",
                'data' => [
                    'id' => $member->id_member,
                    'no_anggota' => $noAnggota,
                    'name' => $member->name,
                    'verification_code' => $verificationCode,
                    'tahun_pembuatan' => $tahunPembuatan,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Member store error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan member: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display member detail
     */
    public function show($id_member)
    {
        $member = Members::where('id_member', $id_member)->firstOrFail();

        if (!$this->isAdminOrPetugas()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        // Eager load borrowing history
        $member->load([
            'borrowing' => fn($q) => $q
                ->with([
                    'books',
                    'bookitems.books',
                    'bookitems.racks'
                ])
                ->latest()
                ->limit(10)
        ]);

        $canViewKtp = $this->isAdminOrPetugas();

        return view('members.show', compact('member', 'canViewKtp'));
    }

    /**
     * Update member (Admin & Petugas only)
     */
    public function update(Request $request, $id_member)
    {
        $member = Members::findOrFail($id_member);

        $member->no_telp = $request->no_telp;
        $member->alamat = $request->alamat;

        $member->save();

        return redirect()->back()->with('success', 'Data member berhasil diperbarui!');
    }


    /**
     * Delete member (Admin & Petugas only)
     */
    public function destroy($id_member)
    {
        $member = Members::where('id_member', $id_member)->firstOrFail();

        if (!$this->isAdminOrPetugas()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin/petugas yang bisa menghapus member'
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Delete files
            if ($member->ktp_photo_path) {
                Storage::disk('private')->delete($member->ktp_photo_path);
            }
            if ($member->photo_path) {
                Storage::disk('private')->delete($member->photo_path);
            }

            \Log::info('Member deleted by staff', [
                'member_id' => $member->id_member,
                'member_name' => $member->name,
                'deleted_by' => auth()->user()->id_user,
            ]);

            $member->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Member berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Member delete error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal hapus member: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify member manually (Admin & Petugas only)
     */
    public function verifyManual($id_member)
    {
        $member = Members::where('id_member', $id_member)->firstOrFail();

        if (!$this->isAdminOrPetugas()) {
            abort(403, 'Hanya admin/petugas yang bisa verifikasi member');
        }

        if ($member->status === 'verified') {
            return back()->with('info', 'Member sudah diverifikasi.');
        }

        DB::beginTransaction();

        try {
            $verificationCode = Members::generateVerificationCode();

            $member->status = 'verified';
            $member->verification_code = $verificationCode;
            $member->admin_verified_at = now();
            $member->verified_by = auth()->user()->id_user;
            $member->save();

            // Send email notification
            Mail::to($member->email)->queue(
                new MemberVerifiedNotification($member, $verificationCode)
            );

            \Log::info('Member verified by staff', [
                'member_id' => $member->id_member,
                'verified_by' => auth()->user()->id_user,
            ]);

            DB::commit();

            return back()->with('success', "✅ Member berhasil diverifikasi! Kode: {$verificationCode}");

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Member verification error: ' . $e->getMessage());
            return back()->with('error', 'Gagal verifikasi: ' . $e->getMessage());
        }
    }

    /**
     * Reject member (Admin & Petugas only)
     */
    public function reject($id_member)
    {
        $member = Members::where('id_member', $id_member)->firstOrFail();

        if (!$this->isAdminOrPetugas()) {
            abort(403, 'Hanya admin/petugas yang bisa reject member');
        }

        $member->status = 'rejected';
        $member->save();

        \Log::info('Member rejected by staff', [
            'member_id' => $member->id_member,
            'rejected_by' => auth()->user()->id_user,
        ]);

        return back()->with('success', 'Member berhasil ditolak.');
    }

    /**
     * Konsumen view their own profile
     */
    public function myProfile()
    {
        $user = auth()->user();

        // Check if user has member record
        $member = Members::where('id_user', $user->id_user)->first();

        if (!$member) {
            return redirect()->route('dashboard')
                ->with('info', 'Anda belum terdaftar sebagai member. Silakan hubungi admin.');
        }

        // Eager load borrowing history
        $member->load([
            'borrowing' => fn($q) => $q
                ->with([
                    'books',
                    'bookitems.books',
                    'bookitems.racks'
                ])
                ->latest()
                ->limit(5)
        ]);

        return view('members.profile', compact('member'));
    }

    /**
     * Download KTP (Admin & Petugas only)
     */
    public function downloadKtp($id_member)
    {
        $member = Members::where('id_member', $id_member)->firstOrFail();

        if (!$this->isAdminOrPetugas()) {
            abort(403, 'Hanya admin/petugas yang bisa download KTP');
        }

        if (!$member->ktp_photo_path || !Storage::disk('private')->exists($member->ktp_photo_path)) {
            abort(404, 'File KTP tidak ditemukan');
        }

        return Storage::disk('private')->download(
            $member->ktp_photo_path,
            'ktp_' . $member->name . '_' . now()->format('Ymd') . '.' . pathinfo($member->ktp_photo_path, PATHINFO_EXTENSION)
        );
    }

    /**
     * Download photo
     */
    public function downloadPhoto($id_member)
    {
        $member = Members::where('id_member', $id_member)->firstOrFail();

        // Admin/Petugas can view any photo, konsumen can only view their own
        if (!$this->isAdminOrPetugas() && auth()->user()->id_user !== $member->id_user) {
            abort(403, 'Anda tidak memiliki akses');
        }

        if (!$member->photo_path || !Storage::disk('private')->exists($member->photo_path)) {
            abort(404, 'Foto tidak ditemukan');
        }

        return Storage::disk('private')->response($member->photo_path);
    }

    /**
     * Verify KTP (Admin & Petugas only)
     */
    public function verifyKtp(Request $request, $id_member)
    {
        $member = Members::where('id_member', $id_member)->firstOrFail();

        if (!$this->isAdminOrPetugas()) {
            abort(403, 'Hanya admin/petugas yang bisa verifikasi KTP');
        }

        $request->validate([
            'verified' => 'required|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $member->ktp_verified_at = $request->verified ? now() : null;
        $member->save();

        \Log::info('KTP verification status changed', [
            'member_id' => $member->id_member,
            'verified' => $request->verified,
            'changed_by' => auth()->user()->id_user,
        ]);

        return back()->with('success', $request->verified ? 'KTP terverifikasi!' : 'Verifikasi KTP dibatalkan!');
    }

    /**
     * Bulk delete members (Admin & Petugas only)
     */
    public function bulkDelete(Request $request)
    {
        if (!$this->isAdminOrPetugas()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin/petugas yang bisa bulk delete'
            ], 403);
        }

        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:members,id_member'
        ]);

        DB::beginTransaction();
        try {
            $members = Members::whereIn('id_member', $request->ids)->get();
            $deletedCount = 0;

            foreach ($members as $member) {
                // Delete files
                if ($member->ktp_photo_path) {
                    Storage::disk('private')->delete($member->ktp_photo_path);
                }
                if ($member->photo_path) {
                    Storage::disk('private')->delete($member->photo_path);
                }

                $member->delete();
                $deletedCount++;
            }

            \Log::info('Bulk delete members', [
                'count' => $deletedCount,
                'deleted_by' => auth()->user()->id_user,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$deletedCount} member!"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Bulk delete error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal hapus: ' . $e->getMessage()
            ], 500);
        }
    }
}
