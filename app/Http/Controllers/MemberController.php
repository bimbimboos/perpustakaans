<?php

namespace App\Http\Controllers;

use App\Models\Members;
use App\Models\User;
use App\Mail\NewMemberNotification;
use App\Mail\MemberVerifiedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function __construct()
    {
        // Kecualikan route create dan store dari middleware auth
        $this->middleware('auth')->except(['create', 'store']);
    }

    private function isAdminOrPetugas()
    {
        return in_array(strtolower(auth()->user()->role), ['admin', 'petugas']);
    }

    private function isOwner($member)
    {
        return auth()->user()->id_user === $member->id_user;
    }

    private function canAccess($member)
    {
        return $this->isAdminOrPetugas() || $this->isOwner($member);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $role = strtolower($user->role);

        if ($role === 'admin' || $role === 'petugas') {
            $query = Members::query()->with(['user', 'verifiedBy']);

            if ($search = $request->input('search')) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('no_telp', 'like', "%{$search}%");
                });
            }

            if ($status = $request->input('status')) {
                $query->where('status', $status);
            }

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

        if ($role === 'konsumen') {
            $member = Members::where('id_user', $user->id_user)->first();

            if ($member) {
                return redirect()->route('members.profile');
            } else {
                return redirect()->route('members.create')
                    ->with('info', 'Silakan daftar sebagai member terlebih dahulu');
            }
        }

        abort(403, 'Unauthorized access');
    }

    public function create()
    {
        $user = auth()->user();

        $existingMember = Members::where('id_user', $user->id_user)->first();

        if ($existingMember) {
            return redirect()
                ->route('members.show', $existingMember->id_member)
                ->with('info', 'Anda sudah terdaftar sebagai member.');
        }

        return view('members.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email',
            'password' => 'required|string|min:8|confirmed',
            'no_telp' => 'required|string|max:20',
            'alamat' => 'required|string|max:500',
            'ktp_number' => 'required|string|size:16',
            'ktp_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'name.required' => 'Nama lengkap wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'no_telp.required' => 'Nomor telepon wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            'ktp_number.required' => 'Nomor KTP wajib diisi',
            'ktp_number.size' => 'Nomor KTP harus 16 digit',
            'ktp_photo.required' => 'Foto KTP wajib diupload',
            'ktp_photo.image' => 'File harus berupa gambar',
            'ktp_photo.max' => 'Ukuran foto KTP maksimal 2MB',
        ]);

        $existingMember = Members::where('id_user', $user->id_user)->first();
        if ($existingMember) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Anda sudah terdaftar sebagai member!');
        }

        $ktpHash = hash('sha256', $request->ktp_number);
        if (Members::where('ktp_hash', $ktpHash)->exists()) {
            return back()->withInput()->withErrors(['ktp_number' => 'Nomor KTP sudah terdaftar!']);
        }

        DB::beginTransaction();

        try {
            $member = new Members();
            $member->id_user = $user->id_user;
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

            if ($request->hasFile('ktp_photo')) {
                $file = $request->file('ktp_photo');
                $filename = 'ktp_' . Str::random(16) . '.' . $file->getClientOriginalExtension();
                $member->ktp_photo_path = $file->storeAs($memberPath, $filename, 'private');
            }

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = 'photo_' . Str::random(16) . '.' . $file->getClientOriginalExtension();
                $member->photo_path = $file->storeAs($memberPath, $filename, 'private');
            }

            $member->save();

            $adminEmail = config('mail.admin_email', 'perpus@example.com');
            Mail::to($adminEmail)->queue(new NewMemberNotification($member));

            \Log::info('New member registered', [
                'member_id' => $member->id_member,
                'name' => $member->name,
                'email' => $member->email,
                'user_id' => $user->id_user,
            ]);

            DB::commit();

            return redirect()
                ->route('dashboard')
                ->with('success', '🎉 Pendaftaran member berhasil! Silakan tunggu verifikasi dari admin (max 1x24 jam).');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Member store error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal mendaftar: ' . $e->getMessage());
        }
    }

    /**
     * SHOW - DETAIL MEMBER
     * (minimal change: eager-load nested relasi buku & rak)
     */
    public function show($id_member)
    {
        $member = Members::where('id_member', $id_member)->firstOrFail();
        $user = auth()->user();
        $role = strtolower($user->role);

        $isAdmin = $role === 'admin';
        $isPetugas = $role === 'petugas';
        $isOwner = $user->id_user === $member->id_user;

        if (!$isAdmin && !$isPetugas && !$isOwner) {
            return redirect()->route('members.index')
                ->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        // --- START: modified eager loading (nested) ---
        // Load borrowing + nested relations:
        // - borrowing.books (direct via id_buku)
        // - borrowing.bookitems.books (via item -> book)
        // - borrowing.bookitems.racks (lokasi rak)
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
        // --- END modified eager loading ---

        $canViewKtp = $isAdmin;

        return view('members.show', compact('member', 'canViewKtp'));
    }

    public function edit($id_member)
    {
        $member = Members::where('id_member', $id_member)->firstOrFail();

        if (!$this->canAccess($member)) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini');
        }

        return view('members.edit', ['member' => $member]);
    }

    public function update(Request $request, $id_member)
    {
        $member = Members::where('id_member', $id_member)->firstOrFail();

        if (!$this->canAccess($member)) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate data ini');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email,' . $member->id_member . ',id_member',
            'password' => 'nullable|string|min:8|confirmed',
            'no_telp' => 'required|string|max:20',
            'alamat' => 'required|string|max:500',
            'ktp_number' => 'nullable|string|size:16',
            'ktp_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $member->fill($request->only(['name', 'email', 'alamat', 'no_telp']));

            if ($request->filled('password')) {
                $member->password = Hash::make($request->password);
            }

            if ($request->filled('ktp_number')) {
                $member->setKtpNumber($request->ktp_number);
                $member->ktp_verified_at = null;
            }

            if ($request->hasFile('ktp_photo')) {
                if ($member->ktp_photo_path) {
                    Storage::disk('private')->delete($member->ktp_photo_path);
                }
                $file = $request->file('ktp_photo');
                $filename = 'ktp_' . Str::random(16) . '.' . $file->getClientOriginalExtension();
                $member->ktp_photo_path = $file->storeAs("members/{$member->id_member}", $filename, 'private');
                $member->ktp_verified_at = null;
            }

            if ($request->hasFile('photo')) {
                if ($member->photo_path) {
                    Storage::disk('private')->delete($member->photo_path);
                }
                $file = $request->file('photo');
                $filename = 'photo_' . Str::random(16) . '.' . $file->getClientOriginalExtension();
                $member->photo_path = $file->storeAs("members/{$member->id_member}", $filename, 'private');
            }

            $member->save();
            DB::commit();

            return redirect()
                ->route('members.show', $member->id_member)
                ->with('success', 'Data member berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy($id_member)
    {
        $member = Members::where('id_member', $id_member)->firstOrFail();

        if (!$this->isAdminOrPetugas()) {
            abort(403, 'Hanya admin/petugas yang bisa menghapus member');
        }

        DB::beginTransaction();
        try {
            if ($member->ktp_photo_path) {
                Storage::disk('private')->delete($member->ktp_photo_path);
            }
            if ($member->photo_path) {
                Storage::disk('private')->delete($member->photo_path);
            }

            $member->delete();
            DB::commit();

            return redirect()
                ->route('members.index')
                ->with('success', 'Member berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal hapus: ' . $e->getMessage());
        }
    }

    public function verify(Request $request, $id_member)
    {
        $token = $request->query('token');
        $member = Members::where('id_member', $id_member)->firstOrFail();

        $expectedToken = sha1($member->email . $member->created_at);
        if ($token !== $expectedToken) {
            abort(403, 'Token verifikasi tidak valid!');
        }

        if ($member->status === 'verified') {
            return redirect()
                ->route('members.index')
                ->with('info', 'Member ini sudah diverifikasi sebelumnya.');
        }

        DB::beginTransaction();

        try {
            $verificationCode = Members::generateVerificationCode();

            $member->status = 'verified';
            $member->verification_code = $verificationCode;
            $member->admin_verified_at = now();
            $member->verified_by = auth()->check() ? auth()->user()->id_user : null;
            $member->save();

            Mail::to($member->email)->queue(
                new MemberVerifiedNotification($member, $verificationCode)
            );

            \Log::info('Member verified', [
                'member_id' => $member->id_member,
                'name' => $member->name,
                'verification_code' => $verificationCode,
                'verified_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->route('members.index')
                ->with('success', "✅ Member {$member->name} berhasil diverifikasi! Email kode verifikasi telah dikirim.");

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Member verification error: ' . $e->getMessage());
            return back()->with('error', 'Gagal verifikasi: ' . $e->getMessage());
        }
    }

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

            Mail::to($member->email)->queue(
                new MemberVerifiedNotification($member, $verificationCode)
            );

            DB::commit();

            return back()->with('success', "✅ Member berhasil diverifikasi! Kode: {$verificationCode}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal verifikasi: ' . $e->getMessage());
        }
    }

    public function reject($id_member)
    {
        $member = Members::where('id_member', $id_member)->firstOrFail();

        if (!$this->isAdminOrPetugas()) {
            abort(403, 'Hanya admin/petugas yang bisa reject member');
        }

        $member->status = 'rejected';
        $member->save();

        return back()->with('success', 'Pendaftar berhasil ditolak.');
    }

    public function profile()
    {
        $user = auth()->user();
        $member = Members::where('id_user', $user->id_user)->firstOrFail();

        // --- START: modified eager loading for profile ---
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
        // --- END modified eager loading ---

        return view('members.profile', compact('member'));
    }

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

    public function downloadPhoto($id_member)
    {
        $member = Members::where('id_member', $id_member)->firstOrFail();

        if (!$this->canAccess($member)) {
            abort(403, 'Anda tidak memiliki akses');
        }

        if (!$member->photo_path || !Storage::disk('private')->exists($member->photo_path)) {
            abort(404, 'Foto tidak ditemukan');
        }

        return Storage::disk('private')->response($member->photo_path);
    }

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

        return back()->with('success', $request->verified ? 'KTP terverifikasi!' : 'Verifikasi KTP dibatalkan!');
    }

    public function bulkDelete(Request $request)
    {
        if (!$this->isAdminOrPetugas()) {
            abort(403, 'Hanya admin/petugas yang bisa bulk delete');
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:members,id_member'
        ]);

        DB::beginTransaction();
        try {
            $members = Members::whereIn('id_member', $request->ids)->get();

            foreach ($members as $member) {
                if ($member->ktp_photo_path) {
                    Storage::disk('private')->delete($member->ktp_photo_path);
                }
                if ($member->photo_path) {
                    Storage::disk('private')->delete($member->photo_path);
                }

                $member->delete();
            }

            DB::commit();

            return redirect()
                ->route('members.index')
                ->with('success', "Berhasil menghapus {$members->count()} member!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal hapus: ' . $e->getMessage());
        }
    }

    public function getEditData($id_member)
    {
        $member = Members::where('id_member', $id_member)->firstOrFail();

        if (!$this->canAccess($member)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json(
            $member->only(['id_member', 'id_user', 'name', 'email', 'no_telp', 'alamat', 'status'])
        );
    }
}
