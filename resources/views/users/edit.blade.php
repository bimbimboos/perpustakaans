@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit User</h1>

        <form action="{{ route('users.update', $user->id_user) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- ROLE --}}
            <div class="mb-4">
                <label for="role" class="form-label">Pilih Role</label>
                <select name="role" id="role" class="form-select">
                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="petugas" {{ $user->role == 'petugas' ? 'selected' : '' }}>Petugas</option>
                    <option value="konsumen" {{ $user->role == 'konsumen' ? 'selected' : '' }}>Konsumen</option>
                </select>
            </div>

            {{-- KTP NUMBER --}}
            <div class="mb-3">
                <label>KTP Number</label>
                <input type="text" name="ktp_number" class="form-control" value="">
                <small class="text-muted">
                    Nomor KTP disimpan terenkripsi. Hanya admin dapat melihat/unduh berkas KTP.
                </small>
            </div>

            {{-- UPLOAD KTP --}}
            <div class="mb-3">
                <label>Upload KTP (jpg,png,pdf, max 5MB)</label>
                <input type="file" name="ktp_photo" class="form-control">

                @if($user->ktp_photo_path)
                    <p class="mt-2">
                        Sudah ada:
                        <a href="{{ route('users.downloadKtp', $user->id_user) }}" target="_blank">Unduh KTP</a>
                    </p>
                @endif
            </div>

            {{-- FOTO PROFIL --}}
            <div class="mb-3">
                <label>Foto Profil</label>
                <input type="file" name="photo" class="form-control">

                @if($user->photo_path)
                    <img src="{{ asset('storage/' . $user->photo_path) }}"
                         alt="photo"
                         style="width:80px;height:80px;object-fit:cover;margin-top:8px;border-radius:8px;">
                @endif
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection
