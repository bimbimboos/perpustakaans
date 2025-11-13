@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg">
                    <div class="card-header bg-gradient-primary text-white">
                        <h4 class="mb-0">📝 Form Pendaftaran Member Perpustakaan</h4>
                    </div>

                    <div class="card-body p-4">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <strong>⚠️ Error:</strong>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('members.store') }}" enctype="multipart/form-data">
                            @csrf

                            <!-- Nama Lengkap -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', auth()->user()->name) }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', auth()->user()->email) }}" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">
                                        Password <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">
                                        Konfirmasi Password <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                            </div>

                            <!-- No Telepon -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    Nomor Telepon <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror"
                                       value="{{ old('no_telp') }}" placeholder="08123456789" required>
                                @error('no_telp')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Alamat -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    Alamat Lengkap <span class="text-danger">*</span>
                                </label>
                                <textarea name="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror" required>{{ old('alamat') }}</textarea>
                                @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nomor KTP -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    Nomor KTP (16 Digit) <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="ktp_number" class="form-control @error('ktp_number') is-invalid @enderror"
                                       value="{{ old('ktp_number') }}" maxlength="16" placeholder="3310XXXXXXXXXXXX" required>
                                @error('ktp_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Upload Foto KTP -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    Upload Foto KTP <span class="text-danger">*</span>
                                </label>
                                <input type="file" name="ktp_photo" class="form-control @error('ktp_photo') is-invalid @enderror"
                                       accept="image/jpeg,image/png,image/jpg" required>
                                <small class="text-muted">Format: JPG, PNG. Max: 2MB</small>
                                @error('ktp_photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Upload Foto Profil (Opsional) -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    Upload Foto Profil (Opsional)
                                </label>
                                <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror"
                                       accept="image/jpeg,image/png,image/jpg">
                                <small class="text-muted">Format: JPG, PNG. Max: 2MB</small>
                                @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Info Box -->
                            <div class="alert alert-info">
                                <strong>ℹ️ Informasi:</strong>
                                <ul class="mb-0">
                                    <li>Data akan diverifikasi oleh admin dalam 1x24 jam</li>
                                    <li>Anda akan menerima email konfirmasi setelah verifikasi</li>
                                    <li>Kode verifikasi akan dikirim via email</li>
                                </ul>
                            </div>

                            <!-- Buttons -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                    ← Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    📤 Daftar Sekarang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
@endsection
