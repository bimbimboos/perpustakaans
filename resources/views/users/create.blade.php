@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Tambah Member Baru</h1>

        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label>Nama</label>
                <input type="text" name="name" class="form-control">
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control">
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
            </div>

            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-select">
                    <option value="konsumen">Konsumen</option>
                    <option value="petugas">Petugas</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="mb-3">
                <label>No KTP</label>
                <input type="text" name="ktp_number" class="form-control">
            </div>

            <div class="mb-3">
                <label>Upload KTP (jpg,png,pdf)</label>
                <input type="file" name="ktp_photo" class="form-control">
            </div>

            <div class="mb-3">
                <label>Foto Profil</label>
                <input type="file" name="photo" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Tambah</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection
