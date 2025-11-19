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
        // Validation sudah dilakukan di FormRequest
        $validated = $request->validated();

        // Check duplicate KTP
        $ktpHash = hash('sha256', $request->ktp_number);
        if (Members::where('ktp_hash', $ktpHash)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor KTP sudah terdaftar!'
            ], 422);
        }

        DB::beginTransaction();

        try {

            /**
             * ===================================================
             * 1. BUAT AKUN USER TERLEBIH DAHULU (WAJIB)
             * ===================================================
             */
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password ?: '12345678'); // default kalau kosong
            $user->role = 'member';
            $user->save();


            /**
             * ===================================================
             * 2. BUAT DATA MEMBER & KAITKAN KE USER
             * ===================================================
             */
            $member = new Members();
            $member->id_user = $user->id_user;        // <= INI YANG HILANG KEMARIN
            $member->name = $request->name;
            $member->email = $request->email;
            $member->password = Hash::make($request->password);
            $member->alamat = $request->alamat;
            $member->no_telp = $request->no_telp;
            $member->role = 'member';
            $member->status = 'pending';
            $member->setKtpNumber($request->ktp_number);
            $member->save();

            $memberPath = "members/{$member->id_member}";


            /**
             * ===================================================
             * 3. UPLOAD KTP
             * ===================================================
             */
            if ($request->hasFile('ktp_photo')) {
                $file = $request->file('ktp_photo');
                $filename = 'ktp_' . Str::random(16) . '.' . $file->getClientOriginalExtension();
                $member->ktp_photo_path = $file->storeAs($memberPath, $filename, 'private');
            }


            /**
             * ===================================================
             * 4. UPLOAD PROFILE PHOTO
             * ===================================================
             */
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = 'photo_' . Str::random(16) . '.' . $file->getClientOriginalExtension();
                $member->photo_path = $file->storeAs($memberPath, $filename, 'private');
            }

            $member->save();


            /**
             * ===================================================
             * 5. LOG & RESPONSE
             * ===================================================
             */
            \Log::info('New member created by staff', [
                'member_id' => $member->id_member,
                'name' => $member->name,
                'email' => $member->email,
                'created_by' => auth()->user()->id_user,
                'created_by_role' => auth()->user()->role,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Member berhasil ditambahkan!',
                'data' => [
                    'id' => $member->id_member,
                    'name' => $member->name,
                    'email' => $member->email,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Member store error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

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
    public function update(UpdateMemberRequest $request, $id_member)
    {
        $member = Members::where('id_member', $id_member)->firstOrFail();

        if (!$this->isAdminOrPetugas()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengupdate data ini'
            ], 403);
        }

        DB::beginTransaction();

        try {
            // Update basic info
            $member->fill($request->only(['name', 'email', 'alamat', 'no_telp']));

            // Update password if provided
            if ($request->filled('password')) {
                $member->password = Hash::make($request->password);
            }

            // Update status (admin only)
            if ($request->filled('status') && auth()->user()->role === 'admin') {
                $member->status = $request->status;
            }

            // Update KTP number if provided
            if ($request->filled('ktp_number')) {
                $ktpHash = hash('sha256', $request->ktp_number);

                // Check if KTP already exists (exclude current member)
                $existingKtp = Members::where('ktp_hash', $ktpHash)
                    ->where('id_member', '!=', $member->id_member)
                    ->exists();

                if ($existingKtp) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Nomor KTP sudah terdaftar!'
                    ], 422);
                }

                $member->setKtpNumber($request->ktp_number);
                $member->ktp_verified_at = null; // Reset verification
            }

            // Update KTP photo
            if ($request->hasFile('ktp_photo')) {
                // Delete old photo
                if ($member->ktp_photo_path) {
                    Storage::disk('private')->delete($member->ktp_photo_path);
                }

                $file = $request->file('ktp_photo');
                $filename = 'ktp_' . Str::random(16) . '.' . $file->getClientOriginalExtension();
                $member->ktp_photo_path = $file->storeAs("members/{$member->id_member}", $filename, 'private');
                $member->ktp_verified_at = null; // Reset verification
            }

            // Update profile photo
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($member->photo_path) {
                    Storage::disk('private')->delete($member->photo_path);
                }

                $file = $request->file('photo');
                $filename = 'photo_' . Str::random(16) . '.' . $file->getClientOriginalExtension();
                $member->photo_path = $file->storeAs("members/{$member->id_member}", $filename, 'private');
            }

            $member->save();

            \Log::info('Member updated by staff', [
                'member_id' => $member->id_member,
                'updated_by' => auth()->user()->id_user,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data member berhasil diupdate!',
                'data' => [
                    'id' => $member->id_member,
                    'name' => $member->name,
                    'status' => $member->status,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Member update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal update member: ' . $e->getMessage()
            ], 500);
        }
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
