<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('role', 'like', "%{$search}%");
        })
            ->orderBy('id_user', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     * (Admin menambahkan user baru)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|min:6',
            'role'           => 'required|in:admin,konsumen',
            'photo'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'ktp_number'     => 'nullable|string|max:32',
            'ktp_photo'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = new User();
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->password = bcrypt($request->password);
        $user->role     = $request->role;

        // Simpan foto profil (optional)
        if ($request->hasFile('photo')) {
            $user->photo = $request->file('photo')->store('users/photo', 'public');
        }

        // Simpan dokumen KTP (terenkripsi)
        if ($request->filled('ktp_number')) {
            $user->ktp_number_enc = Crypt::encryptString($request->ktp_number);
            $user->ktp_hash = hash('sha256', $request->ktp_number);
        }

        if ($request->hasFile('ktp_photo')) {
            $user->ktp_photo_path = $request->file('ktp_photo')->store('users/ktp', 'public');
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User baru berhasil ditambahkan!');
    }

    /**
     * Update user (role, data pribadi, atau KTP)
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id_user . ',id_user',
            'role'       => 'required|in:admin,konsumen',
            'photo'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'ktp_number' => 'nullable|string|max:32',
            'ktp_photo'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->role  = $request->role;

        // Ganti foto profil
        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->photo = $request->file('photo')->store('users/photo', 'public');
        }

        // Ganti data KTP
        if ($request->filled('ktp_number')) {
            $user->ktp_number_enc = Crypt::encryptString($request->ktp_number);
            $user->ktp_hash = hash('sha256', $request->ktp_number);
        }

        if ($request->hasFile('ktp_photo')) {
            if ($user->ktp_photo_path && Storage::disk('public')->exists($user->ktp_photo_path)) {
                Storage::disk('public')->delete($user->ktp_photo_path);
            }
            $user->ktp_photo_path = $request->file('ktp_photo')->store('users/ktp', 'public');
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui!');
    }

    /**
     * Hapus user + file foto & KTP
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }

        if ($user->ktp_photo_path && Storage::disk('public')->exists($user->ktp_photo_path)) {
            Storage::disk('public')->delete($user->ktp_photo_path);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus!');
    }

    /**
     * Download file KTP (untuk admin)
     */
    public function downloadKtp($id)
    {
        $user = User::findOrFail($id);

        if (!$user->ktp_photo_path || !Storage::disk('public')->exists($user->ktp_photo_path)) {
            return redirect()->back()->with('error', 'File KTP tidak ditemukan.');
        }

        return Storage::disk('public')->download($user->ktp_photo_path, 'KTP_' . $user->name . '.jpg');
    }
}
