@extends('layouts.app')

@section('title', 'Edit Member')

@push('styles')
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 2rem;
            margin: -30px -30px 2rem -30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 0.75rem;
        }

        .page-header h1 {
            color: white;
            font-weight: 700;
            margin: 0;
            font-size: 1.75rem;
        }

        .breadcrumb {
            background: transparent;
            margin: 0.5rem 0 0 0;
            padding: 0;
        }

        .breadcrumb-item a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: white;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            color: rgba(255,255,255,0.6);
        }

        .form-card {
            background: var(--card-bg);
            border-radius: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border: 1px solid var(--card-border);
            overflow: hidden;
            margin-bottom: 2rem;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 1.5rem;
            color: white;
        }

        .card-header-custom h5 {
            margin: 0;
            font-weight: 600;
        }

        .form-label {
            font-weight: 600;
            color: var(--body-color);
            margin-bottom: 0.5rem;
            transition: color 0.3s;
        }

        .form-control, .form-select {
            border: 2px solid var(--card-border);
            padding: 0.75rem;
            border-radius: 0.5rem;
            transition: all 0.2s;
            background-color: var(--card-bg);
            color: var(--body-color);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.1);
            background-color: var(--card-bg);
            color: var(--body-color);
        }

        .photo-preview-wrapper {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .photo-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 4px solid var(--card-border);
            object-fit: cover;
            margin-bottom: 1rem;
            transition: border-color 0.3s;
        }

        .btn-action {
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            color: white;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .required-mark {
            color: #e53e3e;
        }

        .text-muted {
            opacity: 0.7;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        @media (max-width: 768px) {
            .page-header {
                margin: -30px -15px 1.5rem -15px;
                padding: 1.5rem;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="page-header fade-in">
        <h1><i class="fas fa-user-edit me-2"></i> Edit Member</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('members.index') }}">Members</a></li>
                <li class="breadcrumb-item"><a href="{{ route('members.show', $member->id_member) }}">{{ $member->name }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <!-- Alert Messages -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show fade-in" role="alert">
            <strong><i class="fas fa-exclamation-triangle me-2"></i> Error!</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-10">

            <form action="{{ route('members.update', $member->id_member) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Personal Information -->
                <div class="form-card fade-in">
                    <div class="card-header-custom">
                        <h5><i class="fas fa-user me-2"></i> Informasi Pribadi</h5>
                    </div>
                    <div class="p-4">

                        <!-- Photo Preview -->
                        <div class="photo-preview-wrapper">
                            @if($member->photo_path)
                                <img src="{{ route('members.download-photo', $member->id_member) }}"
                                     alt="Current Photo"
                                     class="photo-preview"
                                     id="photoPreview">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&size=150&background=667eea&color=fff"
                                     alt="Current Photo"
                                     class="photo-preview"
                                     id="photoPreview">
                            @endif
                            <p class="text-muted">Foto Profil Saat Ini</p>
                        </div>

                        <div class="row">
                            <!-- Nama Lengkap -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Nama Lengkap <span class="required-mark">*</span>
                                </label>
                                <input type="text"
                                       name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $member->name) }}"
                                       required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Email <span class="required-mark">*</span>
                                </label>
                                <input type="email"
                                       name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $member->email) }}"
                                       required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- No. Telepon -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    No. Telepon <span class="required-mark">*</span>
                                </label>
                                <input type="text"
                                       name="no_telp"
                                       class="form-control @error('no_telp') is-invalid @enderror"
                                       value="{{ old('no_telp', $member->no_telp) }}"
                                       required>
                                @error('no_telp')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Role (Admin Only) -->
                            @if(auth()->user()->role === 'admin')
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Role</label>
                                    <select name="role" class="form-select @error('role') is-invalid @enderror">
                                        <option value="member" {{ $member->role === 'member' ? 'selected' : '' }}>Member</option>
                                        <option value="admin" {{ $member->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                        </div>

                        <!-- Alamat -->
                        <div class="mb-3">
                            <label class="form-label">
                                Alamat Lengkap <span class="required-mark">*</span>
                            </label>
                            <textarea name="alamat"
                                      rows="3"
                                      class="form-control @error('alamat') is-invalid @enderror"
                                      required>{{ old('alamat', $member->alamat) }}</textarea>
                            @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status (Admin Only) -->
                        @if(auth()->user()->role === 'admin')
                            <div class="mb-3">
                                <label class="form-label">Status Member</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="pending" {{ $member->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="verified" {{ $member->status === 'verified' ? 'selected' : '' }}>Verified</option>
                                    <option value="rejected" {{ $member->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Upload Foto -->
                <div class="form-card fade-in">
                    <div class="card-header-custom">
                        <h5><i class="fas fa-camera me-2"></i> Ganti Foto Profil</h5>
                    </div>
                    <div class="p-4">
                        <div class="mb-3">
                            <label class="form-label">Upload Foto Baru (Opsional)</label>
                            <input type="file"
                                   name="photo"
                                   class="form-control @error('photo') is-invalid @enderror"
                                   accept="image/*"
                                   onchange="previewNewPhoto(event)">
                            <small class="text-muted">Format: JPG, PNG, JPEG. Maksimal 2MB</small>
                            @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="photo-preview-wrapper">
                            <img id="newPhotoPreview" src="#" alt="Preview Foto Baru"
                                 style="display: none; width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid var(--card-border);">
                            <p id="newPhotoText" class="text-muted">Belum ada foto baru dipilih</p>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="text-center fade-in mb-4">
                    <button type="submit" class="btn btn-primary-custom btn-action me-2">
                        <i class="fas fa-save me-2"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('members.show', $member->id_member) }}" class="btn btn-secondary btn-action">
                        <i class="fas fa-arrow-left me-2"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function previewNewPhoto(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('newPhotoPreview');
            const text = document.getElementById('newPhotoText');

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    text.style.display = 'none';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
                text.style.display = 'block';
            }
        }
    </script>
@endpush
