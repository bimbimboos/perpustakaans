<?php

namespace App\Http\Controllers;

use App\Models\Members;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use App\Notifications\NewMemberRegistered;

class MemberController extends Controller
{
    public function __construct()
    {
        // Middleware auth untuk semua method
        $this->middleware('auth');

        // $this->authorizeResource(Members::class, 'members');
    }

    /**
     * Display listing member (dengan search & filter)
     */
    public function index(Request $request)
    {
        $query = Members::query();

        // Search
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('no_telp', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter verified
        if ($request->has('verified')) {
            if ($request->verified === '1') {
                $query->whereNotNull('ktp_verified_at');
            } else {
                $query->whereNull('ktp_verified_at');
            }
        }

        $members = $query->latest()->paginate(20);

        return view('members.index', compact('members'));
    }

    public function create()
    {
        return view('members.create');
    }

    public function store(StoreMemberRequest $request)
    {
        DB::beginTransaction();

        try {
            $member = new Members();
            $member->name = $request->name;
            $member->email = $request->email;
            $member->password = Hash::make($request->password);
            $member->alamat = $request->alamat;
            $member->no_telp = $request->no_telp;
            $member->role = $request->input('role', 'member');
            $member->status = $request->input('status', 'active');
            $member->setKtpNumber($request->ktp_number);
            $member->save();

            $memberPath = "members/{$member->id_user}";

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
            DB::commit();

            // Kirim notifikasi ke operator/admin
            $operators = Members::whereIn('role', ['operator', 'admin'])->get();

            if ($operators->isNotEmpty()) {
                Notification::send($operators, new NewMemberRegistered($member));
            }

            return redirect()
                ->route('members.show', $member)
                ->with('success', 'Member berhasil ditambahkan dan notifikasi telah dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menambahkan member: ' . $e->getMessage());
        }
    }

    public function show(Members $members)
    {
        $members->load(['borrowings' => fn($q) => $q->latest()->limit(10)]);
        $canViewKtp = auth()->user()->can('viewKtp', $members);

        return view('members.show', ['member' => $members, 'canViewKtp' => $canViewKtp]);
    }

    public function edit(Members $members)
    {
        return view('members.edit', ['member' => $members]);
    }

    public function update(UpdateMemberRequest $request, Members $members)
    {
        DB::beginTransaction();

        try {
            $members->fill($request->only(['name', 'email', 'alamat', 'no_telp', 'role', 'status']));

            if ($request->filled('password')) {
                $members->password = Hash::make($request->password);
            }

            if ($request->filled('ktp_number')) {
                $members->setKtpNumber($request->ktp_number);
                $members->ktp_verified_at = null;
            }

            if ($request->hasFile('ktp_photo')) {
                if ($members->ktp_photo_path) Storage::disk('private')->delete($members->ktp_photo_path);
                $file = $request->file('ktp_photo');
                $filename = 'ktp_' . Str::random(16) . '.' . $file->getClientOriginalExtension();
                $members->ktp_photo_path = $file->storeAs("members/{$members->id_user}", $filename, 'private');
                $members->ktp_verified_at = null;
            }

            if ($request->hasFile('photo')) {
                if ($members->photo_path) Storage::disk('private')->delete($members->photo_path);
                $file = $request->file('photo');
                $filename = 'photo_' . Str::random(16) . '.' . $file->getClientOriginalExtension();
                $members->photo_path = $file->storeAs("members/{$members->id_user}", $filename, 'private');
            }

            $members->save();
            DB::commit();

            return redirect()->route('members.show', $members)->with('success', 'Data member berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mengupdate member: ' . $e->getMessage());
        }
    }

    public function destroy(Members $members)
    {
        DB::beginTransaction();
        try {
            // Hapus files sebelum delete record
            if ($members->ktp_photo_path) {
                Storage::disk('private')->delete($members->ktp_photo_path);
            }
            if ($members->photo_path) {
                Storage::disk('private')->delete($members->photo_path);
            }

            $members->delete();
            DB::commit();

            return redirect()->route('members.index')->with('success', 'Member berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus member: ' . $e->getMessage());
        }
    }

    /**
     * Get member data for edit (AJAX)
     */
    public function getEditData(Members $members)
    {
        $this->authorize('update', $members);

        return response()->json(
            $members->only(['id_user', 'name', 'email', 'no_telp', 'alamat', 'status'])
        );
    }

    /**
     * Bulk delete members
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:members,id_user'
        ]);

        DB::beginTransaction();
        try {
            $members = Members::whereIn('id_user', $request->ids)->get();

            // Cek authorization untuk semua member dulu
            foreach ($members as $member) {
                $this->authorize('delete', $member);
            }

            // Hapus files & records
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
            return back()->with('error', 'Gagal menghapus member: ' . $e->getMessage());
        }
    }

    public function verifyKtp(Request $request, Members $members)
    {
        $this->authorize('verifyKtp', $members);
        $request->validate([
            'verified' => 'required|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $members->ktp_verified_at = $request->verified ? now() : null;
        $members->save();

        return back()->with('success', $request->verified ? 'KTP berhasil diverifikasi!' : 'Verifikasi KTP dibatalkan!');
    }

    public function downloadKtp(Request $request, Members $members)
    {
        $this->authorize('viewKtp', $members);

        if (!$members->ktp_photo_path || !Storage::disk('private')->exists($members->ktp_photo_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('private')->download(
            $members->ktp_photo_path,
            'ktp_' . $members->name . '_' . now()->format('Ymd') . '.' . pathinfo($members->ktp_photo_path, PATHINFO_EXTENSION)
        );
    }

    public function downloadPhoto(Members $members)
    {
        if (auth()->id() !== $members->id_user && auth()->user()->role !== 'admin') {
            abort(403);
        }

        if (!$members->photo_path || !Storage::disk('private')->exists($members->photo_path)) {
            abort(404);
        }

        return Storage::disk('private')->response($members->photo_path);
    }
}
