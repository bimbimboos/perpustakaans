@extends('layouts.app')
@section('content')
    @push('styles')
        <style>
            :root {
                --primary: #667eea;
                --secondary: #764ba2;
                --success: #38a169;
                --warning: #dd6b20;
                --danger: #e53e3e;
                --dark: #121212;
                --darker: #0a0a0a;
                --gray: #1e1e1e;
                --light: #ffffff;
                --border: #333;
            }

            .main-content {
                background: var(--dark);
                min-height: 100vh;
                padding: 2.5rem;
                margin-left: 280px;
                color: #e0e0e0;
            }

            .page-header {
                background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
                border-radius: 16px;
                padding: 2rem 2.5rem;
                margin: -2.5rem -2.5rem 2rem -2.5rem;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            }

            .page-header h1 {
                font-size: 2rem;
                font-weight: 700;
                color: white;
                margin: 0;
            }

            .page-header p {
                color: rgba(255,255,255,0.9);
                font-size: 1rem;
                margin-top: 0.5rem;
                margin-bottom: 0;
            }

            /* Stats Cards */
            .stats-row .stat-card {
                background: rgba(255, 255, 255, 0.05);
                border-radius: 12px;
                padding: 1.5rem;
                border: 1px solid rgba(255,255,255,0.1);
                text-align: center;
            }

            .stat-number {
                font-size: 2.5rem;
                font-weight: 700;
                margin: 0;
                color: var(--primary);
            }

            .stat-label {
                color: #aaa;
                font-size: 0.9rem;
                margin-top: 0.5rem;
            }

            /* Filter Card */
            .filter-card {
                background: rgba(255, 255, 255, 0.05);
                border-radius: 12px;
                padding: 1.5rem;
                border: 1px solid rgba(255,255,255,0.1);
                margin-bottom: 1.5rem;
            }

            /* Table Card */
            .table-card {
                background: rgba(255, 255, 255, 0.05);
                border-radius: 12px;
                overflow: hidden;
                border: 1px solid rgba(255,255,255,0.1);
            }

            .table-header {
                background: rgba(255, 255, 255, 0.1);
                padding: 1.25rem 1.5rem;
                color: white;
                font-size: 1.1rem;
                font-weight: 600;
            }

            .table {
                margin: 0;
                color: #e0e0e0;
            }

            .table thead th {
                background: rgba(0,0,0,0.2);
                color: #e0e0e0;
                border-bottom: 1px solid rgba(255,255,255,0.1);
                padding: 1rem 0.75rem;
                font-weight: 600;
                font-size: 0.9rem;
            }

            .table tbody td {
                padding: 1rem 0.75rem;
                border-bottom: 1px solid rgba(255,255,255,0.05);
                vertical-align: middle;
            }

            .table tbody tr:hover {
                background: rgba(255,255,255,0.03);
            }

            /* Member Photo */
            .member-photo {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                object-fit: cover;
                border: 2px solid rgba(255,255,255,0.2);
            }

            /* BADGE STATUS - FIXED */
            .badge-status {
                padding: 0.4rem 0.8rem;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                border: 1px solid transparent;
            }

            .bg-warning {
                background: rgba(221, 107, 32, 0.2) !important;
                color: #ffa94d !important;
                border-color: rgba(221, 107, 32, 0.3) !important;
            }

            .bg-success {
                background: rgba(56, 161, 105, 0.2) !important;
                color: #69db7c !important;
                border-color: rgba(56, 161, 105, 0.3) !important;
            }

            .bg-danger {
                background: rgba(229, 62, 62, 0.2) !important;
                color: #ff8787 !important;
                border-color: rgba(229, 62, 62, 0.3) !important;
            }

            /* Buttons */
            .btn-action {
                padding: 0.4rem 0.6rem;
                border-radius: 6px;
                font-size: 0.8rem;
                background: rgba(255,255,255,0.1);
                border: 1px solid rgba(255,255,255,0.2);
                color: #e0e0e0;
            }

            .btn-action:hover {
                background: rgba(255,255,255,0.15);
                color: white;
            }

            /* Form Controls */
            .form-control, .form-select {
                background: rgba(255,255,255,0.1);
                border: 1px solid rgba(255,255,255,0.2);
                color: #e0e0e0;
            }

            .form-control:focus, .form-select:focus {
                background: rgba(255,255,255,0.15);
                border-color: var(--primary);
                color: #e0e0e0;
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            }

            .form-label {
                color: #e0e0e0;
                font-weight: 600;
                font-size: 0.9rem;
            }

            .input-group-text {
                background: rgba(255,255,255,0.1);
                border: 1px solid rgba(255,255,255,0.2);
                color: #aaa;
            }

            /* Alerts */
            .alert {
                background: rgba(255,255,255,0.1);
                border: 1px solid rgba(255,255,255,0.2);
                color: #e0e0e0;
                border-radius: 8px;
            }

            /* Loading */
            .loading-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.7);
                display: none;
                justify-content: center;
                align-items: center;
                z-index: 9999;
            }

            .loading-overlay.show {
                display: flex;
            }

            .spinner {
                width: 40px;
                height: 40px;
                border: 4px solid rgba(255,255,255,0.3);
                border-top: 4px solid var(--primary);
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            /* Empty State */
            .empty-state {
                text-align: center;
                padding: 3rem;
                color: #888;
            }

            .empty-state i {
                font-size: 3rem;
                margin-bottom: 1rem;
                opacity: 0.5;
            }

            /* Pagination */
            .pagination .page-link {
                background: rgba(255,255,255,0.1);
                border: 1px solid rgba(255,255,255,0.2);
                color: #e0e0e0;
            }

            .pagination .page-link:hover {
                background: rgba(255,255,255,0.2);
                color: white;
            }

            .pagination .page-item.active .page-link {
                background: var(--primary);
                border-color: var(--primary);
            }

            /* Back Button */
            .btn-lg {
                background: rgba(255,255,255,0.1);
                border: 1px solid rgba(255,255,255,0.2);
                color: #e0e0e0;
                padding: 0.7rem 1.5rem;
            }

            .btn-lg:hover {
                background: rgba(255,255,255,0.2);
                color: white;
            }
        </style>
    @endpush

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
</div>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="fas fa-users me-2"></i> Kelola Member Perpustakaan</h1>
                <p class="text-white-50 mb-0">Verifikasi dan kelola pendaftar member</p>
            </div>
            <div>
                <button class="btn btn-light btn-lg" data-bs-toggle="modal" data-bs-target="#createMemberModal">
                    <i class="fas fa-user-plus me-2"></i> Tambah Member Baru
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5">

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show fade-in" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>{{ session('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show fade-in" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show fade-in" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics -->
    <div class="stats-row row fade-in">
        <div class="col-md-4 mb-3">
            <div class="stat-card total">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-number text-primary">{{ $members->total() }}</p>
                        <p class="stat-label">Total Member</p>
                    </div>
                    <div style="font-size: 3rem; color: var(--primary); opacity: 0.2;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card verified">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-number text-success">{{ \App\Models\Members::where('status', 'verified')->count() }}</p>
                        <p class="stat-label">Member Aktif</p>
                    </div>
                    <div style="font-size: 3rem; color: var(--success); opacity: 0.2;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card total">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-number text-info">{{ \App\Models\Members::whereDate('created_at', today())->count() }}</p>
                        <p class="stat-label">Daftar Hari Ini</p>
                    </div>
                    <div style="font-size: 3rem; color: #00000; opacity: 0.2;">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="filter-card fade-in">
        <form method="GET" action="{{ route('members.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Cari Member</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control"
                           placeholder="Nama, email, atau telepon..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                        Pending
                    </option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>
                        Verified
                    </option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                        Rejected
                    </option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Verifikasi</label>
                <select name="verified" class="form-select">
                    <option value="">Semua</option>
                    <option value="1" {{ request('verified') == '1' ? 'selected' : '' }}>Sudah</option>
                    <option value="0" {{ request('verified') == '0' ? 'selected' : '' }}>Belum</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">&nbsp;</label>
                <div class="d-grid gap-2 d-md-flex">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i> Filter
                    </button>
                    <a href="{{ route('members.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-2"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Members Table -->
    <div class="table-card fade-in">
        <div class="table-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5>
                    <i class="fas fa-list me-2"></i>
                    Daftar Member ({{ $members->total() }} member)
                </h5>
                <div>
                    @if(request('status') === 'pending')
                        <span class="badge bg-warning">
                            <i class="fas fa-filter me-1"></i>
                            Pending: {{ $members->total() }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th width="60">ID</th>
                    <th width="80">Foto</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No. Telp</th>
                    <th width="120">Status</th>
                    <th width="150">Tanggal Daftar</th>
                    <th width="220" class="text-center">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($members as $member)
                    <tr>
                        <td>
                            <strong>#{{ str_pad($member->id_member, 4, '0', STR_PAD_LEFT) }}</strong>
                        </td>
                        <td>
                            @if($member->photo_path)
                                <img src="{{ route('members.download-photo', $member->id_member) }}"
                                     alt="{{ $member->name }}"
                                     class="member-photo">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&size=40&background=667eea&color=fff"
                                     alt="{{ $member->name }}"
                                     class="member-photo">
                            @endif
                        </td>
                        <td>
                            <strong>{{ $member->name }}</strong>
                        </td>
                        <td>
                            <i class="fas fa-envelope text-muted me-1"></i>
                            {{ $member->email }}
                        </td>
                        <td>
                            <i class="fas fa-phone text-muted me-1"></i>
                            {{ $member->no_telp }}
                        </td>
                        <td>
                            @if($member->status === 'pending')
                                <span class="badge bg-warning badge-status">
                                    <i class="fas fa-clock me-1"></i> Pending
                                </span>
                            @elseif($member->status === 'verified')
                                <span class="badge bg-success badge-status">
                                    <i class="fas fa-check-circle me-1"></i> Verified
                                </span>
                            @else
                                <span class="badge bg-danger badge-status">
                                    <i class="fas fa-times-circle me-1"></i> Rejected
                                </span>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                {{ $member->created_at->format('d M Y') }}
                                <br>
                                <i class="fas fa-clock me-1"></i>
                                {{ $member->created_at->format('H:i') }} WIB
                            </small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <!-- View Detail -->
                                <a href="{{ route('members.show', $member->id_member) }}"
                                   class="btn btn-info btn-action"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <!-- Edit Button -->
                                <button type="button"
                                        class="btn btn-primary btn-action"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $member->id_member }}"
                                        title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <!-- Show Verification Code (semua member pasti punya) -->
                                @if($member->verification_code)
                                    <button type="button"
                                            class="btn btn-secondary btn-action"
                                            onclick="showVerificationCode('{{ $member->verification_code }}')"
                                            title="Lihat Kode">
                                        <i class="fas fa-key"></i>
                                    </button>
                                @endif

                                <!-- Delete Button (Admin only) -->
                                @if(auth()->user()->role === 'admin')
                                    <button type="button"
                                            class="btn btn-danger btn-action"
                                            onclick="deleteMember({{ $member->id_member }}, '{{ $member->name }}')"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i class="fas fa-users-slash"></i>
                                <h5>Tidak Ada Data Member</h5>
                                <p class="text-muted">Belum ada pendaftar member</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($members->hasPages())
            <div class="p-3">
                {{ $members->links() }}
            </div>
        @endif
    </div>

    <!-- Back Button -->
    <div class="text-center mt-4">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-lg">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
        </a>
    </div>
</div>

<!-- CREATE MEMBER MODAL -->
<div class="modal fade" id="createMemberModal" tabindex="-1" aria-labelledby="createMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createMemberModalLabel">
                    <i class="fas fa-user-plus me-2"></i> Form Pendaftaran Member Perpustakaan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createMemberForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <!-- INFO BOX -->
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Petunjuk:</strong> Isi form sesuai dengan data yang tertera di KTP/Kartu Pelajar dan formulir fisik yang diberikan calon member.
                    </div>

                    <!-- SECTION 1: DATA PRIBADI -->
                    <div class="border rounded p-3 mb-4" style="background: #f8f9fa;">
                        <h6 class="fw-bold mb-3"><i class="fas fa-user me-2"></i> Data Pribadi</h6>

                        <div class="row g-3">
                            <!-- Nama Lengkap -->
                            <div class="col-md-6">
                                <label for="create_name" class="form-label">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="create_name" name="name"
                                       placeholder="Sesuai KTP/Kartu Pelajar" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Tempat Lahir -->
                            <div class="col-md-3">
                                <label for="create_tempat_lahir" class="form-label">
                                    Tempat Lahir <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="create_tempat_lahir" name="tempat_lahir"
                                       placeholder="Surabaya" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Tanggal Lahir -->
                            <div class="col-md-3">
                                <label for="create_tanggal_lahir" class="form-label">
                                    Tanggal Lahir <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control" id="create_tanggal_lahir" name="tanggal_lahir" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- No Identitas -->
                            <div class="col-md-6">
                                <label for="create_ktp_number" class="form-label">
                                    No. Identitas (KTP/Kartu Pelajar/NIS) <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="create_ktp_number" name="ktp_number"
                                       placeholder="Contoh: 3578XXXXXXXXXXXX atau NIS: 123456" required>
                                <small class="text-muted">Bisa berupa No. KTP (16 digit) atau No. Kartu Pelajar/NIS</small>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Agama -->
                            <div class="col-md-6">
                                <label for="create_agama" class="form-label">
                                    Agama <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="create_agama" name="agama" required>
                                    <option value="">-- Pilih Agama --</option>
                                    <option value="Islam">Islam</option>
                                    <option value="Kristen">Kristen</option>
                                    <option value="Katolik">Katolik</option>
                                    <option value="Hindu">Hindu</option>
                                    <option value="Buddha">Buddha</option>
                                    <option value="Konghucu">Konghucu</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <!-- Gender -->
                            <div class="col-md-6">
                                <label for="create_gender" class="form-label">
                                    Jenis Kelamin <span class="text-danger">*</span>
                                </label>
                            <select name="gender" class="form-control" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Alamat -->
                            <div class="col-12">
                                <label for="create_alamat" class="form-label">
                                    Alamat Lengkap (Sesuai KTP/Kartu Pelajar) <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="create_alamat" name="alamat" rows="2"
                                          placeholder="Jl. Contoh No. 123, Kelurahan, Kecamatan, Kota" required></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: DATA PENDIDIKAN/INSTITUSI -->
                    <div class="border rounded p-3 mb-4" style="background: #f8f9fa;">
                        <h6 class="fw-bold mb-3"><i class="fas fa-graduation-cap me-2"></i> Data Pendidikan/Institusi</h6>

                        <div class="row g-3">
                            <!-- Jenjang Pendidikan -->
                            <div class="col-md-6">
                                <label for="create_jenjang_pendidikan" class="form-label">
                                    Jenjang Pendidikan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="create_jenjang_pendidikan" name="jenjang_pendidikan" required>
                                    <option value="">-- Pilih Jenjang --</option>
                                    <option value="SD">SD</option>
                                    <option value="SMP">SMP</option>
                                    <option value="SMA/SMK">SMA/SMK</option>
                                    <option value="D3">D3</option>
                                    <option value="S1">S1</option>
                                    <option value="S2">S2</option>
                                    <option value="S3">S3</option>
                                    <option value="Umum">Umum (Bukan Pelajar/Mahasiswa)</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Institusi -->
                            <div class="col-md-6">
                                <label for="create_institusi" class="form-label">
                                    Nama Sekolah/Universitas/Instansi
                                </label>
                                <input type="text" class="form-control" id="create_institusi" name="institusi"
                                       placeholder="Contoh: SMAN 1 Surabaya / Universitas Airlangga">
                                <small class="text-muted">Kosongkan jika tidak ada</small>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Alamat Institusi -->
                            <div class="col-12">
                                <label for="create_alamat_institusi" class="form-label">
                                    Alamat Sekolah/Universitas/Instansi
                                </label>
                                <textarea class="form-control" id="create_alamat_institusi" name="alamat_institusi" rows="2"
                                          placeholder="Alamat lengkap institusi"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: KONTAK -->
                    <div class="border rounded p-3 mb-4" style="background: #f8f9fa;">
                        <h6 class="fw-bold mb-3"><i class="fas fa-phone me-2"></i> Data Kontak</h6>

                        <div class="row g-3">
                            <!-- Email (opsional) -->
                            <div class="col-md-6">
                                <label for="create_email" class="form-label">
                                    Email (Opsional)
                                </label>
                                <input type="email" class="form-control" id="create_email" name="email"
                                       placeholder="contoh@email.com">
                                <small class="text-muted">Boleh dikosongkan</small>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- No HP Member -->
                            <div class="col-md-6">
                                <label for="create_no_telp" class="form-label">
                                    No. HP Member <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="create_no_telp" name="no_telp"
                                       placeholder="08123456789" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- No HP Orang Tua/Wali -->
                            <div class="col-md-6">
                                <label for="create_no_hp_ortu" class="form-label">
                                    No. HP Orang Tua/Wali
                                </label>
                                <input type="text" class="form-control" id="create_no_hp_ortu" name="no_hp_ortu"
                                       placeholder="08123456789">
                                <small class="text-muted">Wajib untuk pelajar/mahasiswa</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 4: UPLOAD DOKUMEN -->
                    <div class="border rounded p-3 mb-4" style="background: #f8f9fa;">
                        <h6 class="fw-bold mb-3"><i class="fas fa-camera me-2"></i> Upload Dokumen</h6>

                        <div class="row g-3">
                            <!-- Upload Foto KTP/Kartu Pelajar -->
                            <div class="col-md-6">
                                <label for="create_ktp_photo" class="form-label">
                                    Foto KTP/Kartu Pelajar <span class="text-danger">*</span>
                                </label>
                                <input type="file" class="form-control" id="create_ktp_photo" name="ktp_photo"
                                       accept="image/jpeg,image/png,image/jpg" required
                                       onchange="previewImage(this, 'ktpPreview')">
                                <small class="text-muted">Format: JPG, PNG. Max: 4MB</small>
                                <div class="invalid-feedback"></div>
                                <div class="mt-2">
                                    <img id="ktpPreview" class="image-preview" alt="Preview KTP"
                                         style="max-width: 100%; max-height: 200px; border-radius: 8px; display: none;">
                                </div>
                            </div>

                            <!-- Upload Pas Foto -->
                            <div class="col-md-6">
                                <label for="create_photo" class="form-label">
                                    Pas Foto 3x4 (Opsional)
                                </label>
                                <input type="file" class="form-control" id="create_photo" name="photo"
                                       accept="image/jpeg,image/png,image/jpg"
                                       onchange="previewImage(this, 'photoPreview')">
                                <small class="text-muted">Format: JPG, PNG. Max: 4MB</small>
                                <div class="invalid-feedback"></div>
                                <div class="mt-2">
                                    <img id="photoPreview" class="image-preview" alt="Preview Photo"
                                         style="max-width: 100%; max-height: 200px; border-radius: 8px; display: none;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- INFO OTOMATIS -->
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Otomatis terisi:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>No. Anggota:</strong> Akan di-generate otomatis (Format: TAHUN-0001)</li>
                            <li><strong>Tahun Pembuatan:</strong> {{ date('Y') }}</li>
                            <li><strong>Kode Verifikasi:</strong> Akan muncul setelah member berhasil disimpan</li>
                            <li><strong>Status:</strong> Langsung Verified (Terverifikasi)</li>
                        </ul>
                    </div>

                </div>
                <!-- SCROLL FIX -->
                <style>
                    .modal-dialog-scrollable .modal-body {
                        max-height: calc(100vh - 150px);
                        overflow-y: auto;
                    }
                </style>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Simpan & Generate Kode Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SCRIPT VALIDASI & PREVIEW -->
<script>
    function previewImage(input, previewId) {
        const file = input.files[0];
        const preview = document.getElementById(previewId);

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = "block";
            };
            reader.readAsDataURL(file);
        } else {
            preview.src = "";
            preview.style.display = "none";
        }
    }

    // VALIDASI FORM

    document.getElementById('createMemberForm').addEventListener('submit', function (e) {
        let valid = true;

        // Validasi KTP
        const ktp = document.getElementById('create_ktp_number');
        if (ktp.value.length !== 16 || isNaN(ktp.value)) {
            ktp.classList.add("is-invalid");
            ktp.nextElementSibling.textContent = "Nomor KTP harus 16 digit angka.";
            valid = false;
        } else {
            ktp.classList.remove("is-invalid");
        }

        // Validasi Foto KTP max 2MB
        const ktpFile = document.getElementById('create_ktp_photo').files[0];
        if (ktpFile && ktpFile.size > 2 * 1024 * 1024) {
            alert("Ukuran Foto KTP maksimal 2MB");
            valid = false;
        }

        // Validasi Foto Profil max 2MB
        const photoFile = document.getElementById('create_photo').files[0];
        if (photoFile && photoFile.size > 2 * 1024 * 1024) {
            alert("Ukuran Foto Profil maksimal 2MB");
            valid = false;
        }

        if (!valid) e.preventDefault();
    });
</script>

<!-- EDIT MODALS (One per Member) -->
@foreach($members as $member)
    <div class="modal fade" id="editModal{{ $member->id_member }}" tabindex="-1" aria-labelledby="editModalLabel{{ $member->id_member }}" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel{{ $member->id_member }}">
                        <i class="fas fa-edit me-2"></i> Edit Member: {{ $member->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editMemberForm{{ $member->id_member }}" class="editMemberForm" data-id="{{ $member->id_member }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Current Photo Preview -->
                            <div class="col-12 text-center mb-3">
                                @if($member->photo_path)
                                    <img src="{{ route('members.download-photo', $member->id_member) }}"
                                         alt="{{ $member->name }}"
                                         class="rounded-circle"
                                         style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #e2e8f0;">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&size=100&background=667eea&color=fff"
                                         alt="{{ $member->name }}"
                                         class="rounded-circle"
                                         style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #e2e8f0;">
                                @endif
                                <p class="text-muted small mt-2">Foto Saat Ini</p>
                            </div>

                            <!-- Nama -->
                            <div class="col-md-6">
                                <label for="edit_name{{ $member->id_member }}" class="form-label">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="edit_name{{ $member->id_member }}"
                                       name="name" value="{{ $member->name }}" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="edit_email{{ $member->id_member }}" class="form-label">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control" id="edit_email{{ $member->id_member }}"
                                       name="email" value="{{ $member->email }}" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Password (Optional for update) -->
                            <div class="col-md-6">
                                <label for="edit_password{{ $member->id_member }}" class="form-label">
                                    Password Baru (Kosongkan jika tidak ingin mengubah)
                                </label>
                                <input type="password" class="form-control" id="edit_password{{ $member->id_member }}"
                                       name="password" placeholder="Kosongkan jika tidak diubah">
                                <small class="text-muted">Min. 8 karakter, harus ada huruf besar, kecil, dan angka</small>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- No. Telepon -->
                            <div class="col-md-6">
                                <label for="edit_no_telp{{ $member->id_member }}" class="form-label">
                                    No. Telepon <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="edit_no_telp{{ $member->id_member }}"
                                       name="no_telp" value="{{ $member->no_telp }}" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Alamat -->
                            <div class="col-12">
                                <label for="edit_alamat{{ $member->id_member }}" class="form-label">
                                    Alamat Lengkap <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="edit_alamat{{ $member->id_member }}"
                                          name="alamat" rows="3" required>{{ $member->alamat }}</textarea>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Status (Admin only) -->
                            @if(auth()->user()->role === 'admin')
                                <div class="col-md-6">
                                    <label for="edit_status{{ $member->id_member }}" class="form-label">
                                        Status Member
                                    </label>
                                    <select class="form-select" id="edit_status{{ $member->id_member }}" name="status">
                                        <option value="pending" {{ $member->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="verified" {{ $member->status == 'verified' ? 'selected' : '' }}>Verified</option>
                                        <option value="rejected" {{ $member->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                            @endif

                            <!-- Upload Foto Baru (Opsional) -->
                            <div class="col-12">
                                <label for="edit_photo{{ $member->id_member }}" class="form-label">
                                    Upload Foto Profil Baru (Opsional)
                                </label>
                                <input type="file" class="form-control" id="edit_photo{{ $member->id_member }}"
                                       name="photo" accept="image/*"
                                       onchange="previewImage(this, 'editPhotoPreview{{ $member->id_member }}')">
                                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto. Format: JPG, PNG. Max: 2MB</small>
                                <div class="invalid-feedback"></div>
                                <img id="editPhotoPreview{{ $member->id_member }}" class="image-preview" alt="Preview">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Setup CSRF Token untuk AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // ========== CREATE MEMBER FORM SUBMISSION ==========
    $('#createMemberForm').on('submit', function(e) {
        e.preventDefault();

        // Clear previous errors
        $('.form-control, .form-select').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        // Show loading
        $('#loadingOverlay').addClass('show');

        const formData = new FormData(this);

        $.ajax({
            url: "{{ route('members.store') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#loadingOverlay').removeClass('show');

                if (response.success) {
                    // Close modal
                    $('#createMemberModal').modal('hide');

                    // Reset form
                    $('#createMemberForm')[0].reset();
                    $('#ktpPreview, #photoPreview').hide();

                    // ✅ TAMPILKAN NO ANGGOTA & KODE VERIFIKASI
                    Swal.fire({
                        icon: 'success',
                        title: 'Member Berhasil Ditambahkan!',
                        html: `
                        <div style="text-align: left; padding: 20px;">
                            <h5 style="margin-bottom: 20px;">📋 Data Member Baru:</h5>

                            <table style="width: 100%; margin-bottom: 20px;">
                                <tr>
                                    <td style="padding: 8px; font-weight: 600;">Nama:</td>
                                    <td style="padding: 8px;">${response.data.name}</td>
                                </tr>
                                <tr style="background: #f8f9fa;">
                                    <td style="padding: 8px; font-weight: 600;">No. Anggota:</td>
                                    <td style="padding: 8px;"><strong>${response.data.no_anggota}</strong></td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px; font-weight: 600;">Tahun Pembuatan:</td>
                                    <td style="padding: 8px;">${response.data.tahun_pembuatan}</td>
                                </tr>
                            </table>

                            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                        padding: 20px; border-radius: 12px; text-align: center; color: white; margin-bottom: 15px;">
                                <p style="margin: 0; font-size: 14px; opacity: 0.9;">Kode Verifikasi:</p>
                                <h1 style="margin: 10px 0; letter-spacing: 0.5rem; font-weight: 700;">
                                    ${response.data.verification_code}
                                </h1>
                            </div>

                            <div style="background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107;">
                                <p style="margin: 0; color: #856404;">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>PENTING:</strong> Catat kode verifikasi ini untuk dicetak di kartu member!
                                </p>
                            </div>
                        </div>
                    `,
                        width: 600,
                        confirmButtonText: 'OK, Cetak Kartu Member',
                        confirmButtonColor: '#667eea',
                        showCancelButton: true,
                        cancelButtonText: 'Tutup',
                        timer: 15000,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirect ke halaman detail member untuk cetak kartu
                            window.location.href = `/members/${response.data.id}`;
                        } else {
                            location.reload();
                        }
                    });
                }
            },
            error: function(xhr) {
                $('#loadingOverlay').removeClass('show');

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors || {};

                    Object.keys(errors).forEach(function(key) {
                        const input = $(`#create_${key}`);
                        input.addClass('is-invalid');
                        const feedback = input.closest('.col-md-6, .col-md-3, .col-12')
                            .find('.invalid-feedback')
                            .first();
                        feedback.text(errors[key][0]);
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Mohon periksa kembali form yang Anda isi',
                        confirmButtonText: 'OK'
                    });
                } else {
                    const message = xhr.responseJSON?.message || 'Terjadi kesalahan saat menambahkan member';

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: message,
                        confirmButtonText: 'OK'
                    });
                }
            }
        });
    });


    // ========== EDIT MEMBER FORM SUBMISSION ==========
    $('.editMemberForm').on('submit', function(e) {
        e.preventDefault();

        const memberId = $(this).data('id');
        const form = $(this);

        // Clear previous errors
        form.find('.form-control').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');

        // Show loading
        $('#loadingOverlay').addClass('show');

        const formData = new FormData(this);

        $.ajax({
            url: `/members/${memberId}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#loadingOverlay').removeClass('show');

                if (response.success) {
                    // Close modal
                    $(`#editModal${memberId}`).modal('hide');

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        // Reload page to show updated data
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                $('#loadingOverlay').removeClass('show');

                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors || {};

                    // Display validation errors
                    Object.keys(errors).forEach(function(key) {
                        const input = form.find(`[name="${key}"]`);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(errors[key][0]);
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Mohon periksa kembali form yang Anda isi',
                        confirmButtonText: 'OK'
                    });
                } else {
                    const message = xhr.responseJSON?.message || 'Terjadi kesalahan saat mengupdate member';

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: message,
                        confirmButtonText: 'OK'
                    });
                }
            }
        });
    });

    // ========== DELETE MEMBER ==========
    function deleteMember(memberId, memberName) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `<p>Yakin ingin menghapus member <strong>${memberName}</strong>?</p>
                   <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> Aksi ini tidak bisa dibatalkan!</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e53e3e',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash me-2"></i> Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#loadingOverlay').addClass('show');

                $.ajax({
                    url: `/members/${memberId}`,
                    type: 'DELETE',
                    success: function(response) {
                        $('#loadingOverlay').removeClass('show');

                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        $('#loadingOverlay').removeClass('show');

                        const message = xhr.responseJSON?.message || 'Gagal menghapus member';

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: message,
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    }

    // ========== VERIFY MEMBER ==========
    function verifyMember(memberId, memberName, memberEmail) {
        Swal.fire({
            title: 'Verifikasi Member',
            html: `<p>Verifikasi member <strong>${memberName}</strong>?</p>
                   <p class="text-info"><i class="fas fa-envelope"></i> Sistem akan otomatis mengirim email berisi kode verifikasi ke:</p>
                   <p><strong>${memberEmail}</strong></p>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#38a169',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-check me-2"></i> Ya, Verifikasi!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#loadingOverlay').addClass('show');

                $.ajax({
                    url: `/members/${memberId}/verify`,
                    type: 'POST',
                    success: function(response) {
                        $('#loadingOverlay').removeClass('show');
                        location.reload();
                    },
                    error: function(xhr) {
                        $('#loadingOverlay').removeClass('show');

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal memverifikasi member',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    }

    // ========== REJECT MEMBER ==========
    function rejectMember(memberId, memberName) {
        Swal.fire({
            title: 'Tolak Member',
            html: `<p>Tolak pendaftaran <strong>${memberName}</strong>?</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dd6b20',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-times me-2"></i> Ya, Tolak!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#loadingOverlay').addClass('show');

                $.ajax({
                    url: `/members/${memberId}/reject`,
                    type: 'POST',
                    success: function(response) {
                        $('#loadingOverlay').removeClass('show');
                        location.reload();
                    },
                    error: function(xhr) {
                        $('#loadingOverlay').removeClass('show');

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal menolak member',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    }

    // ========== SHOW VERIFICATION CODE ==========
    function showVerificationCode(code) {
        Swal.fire({
            title: 'Kode Verifikasi',
            html: `<div style='font-size:2.5rem;font-weight:700;letter-spacing:0.5rem;color:#667eea;'>${code}</div>
                   <p class="text-muted mt-3"><i class="fas fa-info-circle"></i> Kode ini telah dikirim via email ke member</p>`,
            icon: 'info',
            confirmButtonText: 'OK'
        });
    }

    // ========== IMAGE PREVIEW ==========
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
        }
    }

    // ========== AUTO DISMISS ALERTS ==========
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // ========== SUCCESS MESSAGE FROM SESSION ==========
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session("success") }}',
        timer: 3000,
        showConfirmButton: false
    });
    @endif

    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '{{ session("error") }}',
        timer: 3000,
        showConfirmButton: false
    });
    @endif
@endsection

