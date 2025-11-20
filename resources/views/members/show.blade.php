@extends('layouts.app')

@section('title', 'Detail Member')

@push('styles')
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --success: #38a169;
            --warning: #dd6b20;
            --danger: #e53e3e;
            --info: #3182ce;
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

        .member-card {
            background: var(--card-bg);
            border-radius: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border: 1px solid var(--card-border);
            overflow: hidden;
            margin-bottom: 1.5rem;
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

        .photo-section {
            text-align: center;
            padding: 2rem;
            background: var(--table-header-bg);
            transition: background-color 0.3s;
        }

        .member-photo {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            border: 5px solid var(--card-bg);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            object-fit: cover;
            margin-bottom: 1rem;
        }

        .info-table {
            width: 100%;
        }

        .info-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--card-border);
            color: var(--body-color);
            transition: color 0.3s, border-color 0.3s;
        }

        .info-table td:first-child {
            font-weight: 600;
            color: var(--sidebar-color);
            width: 200px;
            opacity: 0.8;
        }

        .info-table tr:last-child td {
            border-bottom: none;
        }

        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 0.875rem;
            display: inline-block;
        }

        .badge-verified {
            background: #f0fff4;
            color: var(--success);
        }

        .badge-pending {
            background: #fffaf0;
            color: var(--warning);
        }

        .badge-rejected {
            background: #fff5f5;
            color: var(--danger);
        }

        .btn-action {
            padding: 0.625rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .ktp-preview {
            max-width: 100%;
            border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .verification-code-display {
            background: linear-gradient(135deg, var(--success) 0%, #48bb78 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            text-align: center;
            margin: 1rem;
        }

        .verification-code-display .code {
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: 0.5rem;
            margin: 0.5rem 0;
        }

        /* MODAL CARD STYLES */
        .modal-card {
            max-width: 1000px;
        }

        .card-3d-container {
            perspective: 2000px;
            width: 100%;
            max-width: 700px;
            height: 440px;
            margin: 0 auto 30px;
        }

        .card-wrapper {
            width: 100%;
            height: 100%;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.8s cubic-bezier(0.4, 0.0, 0.2, 1);
            cursor: pointer;
        }

        .card-wrapper:hover {
            transform: scale(1.02);
        }

        .card-wrapper.flipped {
            transform: rotateY(180deg);
        }

        .card-face {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .card-front {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card-back {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            transform: rotateY(180deg);
        }

        /* FRONT DESIGN */
        .front-content {
            padding: 35px;
            height: 100%;
            color: white;
            position: relative;
        }

        .logo-area {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 25px;
        }

        .logo {
            width: 70px;
            height: 70px;
            background: white;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 35px;
            color: #667eea;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .library-name h2 {
            font-size: 26px;
            margin-bottom: 5px;
            font-weight: 700;
        }

        .library-name p {
            font-size: 13px;
            opacity: 0.9;
        }

        .member-photo-wrapper {
            position: absolute;
            top: 35px;
            right: 35px;
            width: 120px;
            height: 145px;
            border: 4px solid white;
            border-radius: 12px;
            overflow: hidden;
            background: white;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        .member-photo-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .member-info-card {
            background: rgba(255,255,255,0.25);
            backdrop-filter: blur(15px);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 18px;
        }

        .member-data-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 12px;
        }

        .member-data-row:last-child {
            margin-bottom: 0;
        }

        .data-label {
            opacity: 0.9;
            font-weight: 500;
        }

        .data-value {
            font-weight: 700;
            text-align: right;
        }

        .member-name-display {
            font-size: 22px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .barcode-section {
            background: white;
            padding: 18px;
            border-radius: 12px;
            text-align: center;
        }

        .barcode-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 8px;
        }

        .barcode-digits {
            display: flex;
            justify-content: center;
            gap: 2px;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            font-weight: 700;
            color: #333;
            letter-spacing: 1px;
        }

        .barcode-digit {
            padding: 2px 4px;
        }

        /* BACK DESIGN */
        .back-content {
            padding: 35px;
            height: 100%;
            color: #333;
        }

        .back-header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
        }

        .back-header h3 {
            font-size: 20px;
            color: #667eea;
            margin-bottom: 5px;
            font-weight: 700;
        }

        .back-header p {
            font-size: 12px;
            color: #666;
        }

        .rules {
            margin-bottom: 18px;
        }

        .rules ol {
            padding-left: 22px;
            margin: 0;
        }

        .rules li {
            font-size: 11px;
            margin-bottom: 6px;
            line-height: 1.5;
            color: #444;
        }

        .contact-info {
            background: rgba(102, 126, 234, 0.1);
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 12px;
        }

        .contact-info h4 {
            font-size: 13px;
            color: #667eea;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .contact-item {
            font-size: 10px;
            margin-bottom: 5px;
            color: #555;
        }

        .found-notice {
            text-align: center;
            font-size: 10px;
            color: #e53e3e;
            font-weight: 600;
            padding: 10px;
            background: rgba(229, 62, 62, 0.1);
            border-radius: 8px;
        }

        .flip-hint {
            text-align: center;
            color: #667eea;
            margin: 20px 0;
            font-size: 14px;
            font-weight: 600;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 1; }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        .modal-card-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .page-header {
                margin: -30px -15px 1.5rem -15px;
                padding: 1.5rem;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }

            .info-table td:first-child {
                width: 100%;
                display: block;
                padding-bottom: 0.25rem;
            }
            .info-table td:last-child {
                width: 100%;
                display: block;
                padding-top: 0.25rem;
            }

            .card-3d-container {
                height: 350px;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="page-header fade-in">
        <h1><i class="fas fa-user-circle me-2"></i> Detail Member</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('members.index') }}">Members</a></li>
                <li class="breadcrumb-item active">{{ $member->name }}</li>
            </ol>
        </nav>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show fade-in" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
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

    <div class="row">
        <!-- Profile Card -->
        <div class="col-lg-4">
            <div class="member-card fade-in">
                <div class="photo-section">
                    @if($member->photo_path)
                        <img src="{{ route('members.download-photo', $member->id_member) }}"
                             alt="{{ $member->name }}"
                             class="member-photo">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&size=200&background=667eea&color=fff"
                             alt="{{ $member->name }}"
                             class="member-photo">
                    @endif

                    <h4 class="mb-2">{{ $member->name }}</h4>
                    <p class="text-muted mb-3">
                        <i class="fas fa-id-badge me-1"></i>
                        ID: #{{ str_pad($member->id_member, 6, '0', STR_PAD_LEFT) }}
                    </p>

                    <!-- Status Badge -->
                    @if($member->status === 'verified')
                        <span class="badge-status badge-verified">
                        <i class="fas fa-check-circle me-1"></i> Terverifikasi
                    </span>
                    @elseif($member->status === 'pending')
                        <span class="badge-status badge-pending">
                        <i class="fas fa-clock me-1"></i> Menunggu Verifikasi
                    </span>
                    @else
                        <span class="badge-status badge-rejected">
                        <i class="fas fa-times-circle me-1"></i> Ditolak
                    </span>
                    @endif
                </div>

                <!-- Verification Code (if verified) -->
                @if($member->status === 'verified' && $member->verification_code)
                    <div class="verification-code-display">
                        <small>Kode Verifikasi:</small>
                        <div class="code">{{ $member->verification_code }}</div>
                        <small><i class="fas fa-info-circle me-1"></i> Simpan kode ini</small>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="member-card fade-in">
                <div class="p-3 d-grid gap-2">
                    @if(auth()->user()->role === 'admin' || auth()->user()->id_user === $member->id_user)
                        <button class="btn btn-primary btn-action" data-bs-toggle="modal" data-bs-target="#editMemberModal">
                            <i class="fas fa-edit me-2"></i> Edit Data
                        </button>
                    @endif

                    <!-- TOMBOL LIHAT KARTU - ONLY FOR VERIFIED -->
                    @if($member->status === 'verified')
                        <button class="btn btn-info btn-action" data-bs-toggle="modal" data-bs-target="#cardModal">
                            <i class="fas fa-id-card me-2"></i> Lihat Kartu Member
                        </button>
                    @endif

                    @if(auth()->user()->role === 'admin' && $member->status === 'pending')
                        <form action="{{ route('members.verify-manual', $member->id_member) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-action w-100"
                                    onclick="return confirm('Verifikasi member ini?')">
                                <i class="fas fa-check me-2"></i> Verifikasi Sekarang
                            </button>
                        </form>

                        <form action="{{ route('members.reject', $member->id_member) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-action w-100"
                                    onclick="return confirm('Tolak member ini?')">
                                <i class="fas fa-times me-2"></i> Tolak Pendaftar
                            </button>
                        </form>
                    @endif

                    @if(auth()->user()->role === 'admin')
                        <form action="{{ route('members.destroy', $member->id_member) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-action w-100"
                                    onclick="return confirm('Yakin hapus member ini?')">
                                <i class="fas fa-trash me-2"></i> Hapus Member
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('members.index') }}" class="btn btn-secondary btn-action">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Detail Information -->
        <div class="col-lg-8">
            <!-- Personal Information -->
            <div class="member-card fade-in">
                <div class="card-header-custom">
                    <h5><i class="fas fa-user me-2"></i> Informasi Pribadi</h5>
                </div>
                <div class="p-3">
                    <table class="info-table">
                        <tr>
                            <td>Nama Lengkap</td>
                            <td><strong>{{ $member->name }}</strong></td>
                        </tr>
                        <tr>
                            <td>Tempat, Tanggal Lahir</td>
                            <td>{{ $member->tempat_lahir }}, {{ \Carbon\Carbon::parse($member->tanggal_lahir)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td>Jenis Kelamin</td>
                            <td>
                                @if($member->gender === 'L')
                                    <span class="badge bg-primary">Laki-laki</span>
                                @elseif($member->gender === 'P')
                                    <span class="badge" style="background-color: #e91e63;">Perempuan</span>
                                @else
                                    <span class="badge bg-secondary">Belum diisi</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Agama</td>
                            <td>{{ $member->agama }}</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>{{ $member->email }}</td>
                        </tr>
                        <tr>
                            <td>No. Telepon</td>
                            <td>{{ $member->no_telp }}</td>
                        </tr>
                        <tr>
                            <td>No. HP Orang Tua</td>
                            <td>{{ $member->no_hp_ortu ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>{{ $member->alamat }}</td>
                        </tr>
                        <tr>
                            <td>Institusi</td>
                            <td>{{ $member->institusi }}</td>
                        </tr>
                        <tr>
                            <td>Alamat Institusi</td>
                            <td>{{ $member->alamat_institusi }}</td>
                        </tr>
                        <tr>
                            <td>Jenjang Pendidikan</td>
                            <td><span class="badge bg-success">{{ $member->jenjang_pendidikan }}</span></td>
                        </tr>
                        <tr>
                            <td>Role</td>
                            <td><span class="badge bg-info">{{ ucfirst($member->role) }}</span></td>
                        </tr>
                        <tr>
                            <td>Tanggal Daftar</td>
                            <td>{{ $member->created_at->format('d F Y, H:i') }} WIB</td>
                        </tr>
                        @if($member->admin_verified_at)
                            <tr>
                                <td>Diverifikasi Pada</td>
                                <td>{{ $member->admin_verified_at->format('d F Y, H:i') }} WIB</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- KTP Information (Admin Only) -->
            @if(($canViewKtp ?? false) || auth()->user()->role === 'admin')
                <div class="member-card fade-in">
                    <div class="card-header-custom">
                        <h5><i class="fas fa-id-card me-2"></i> Informasi KTP</h5>
                    </div>
                    <div class="p-3">
                        @if($member->ktp_photo_path)
                            <div class="mb-3">
                                <img src="{{ route('members.download-ktp', $member->id_member) }}"
                                     alt="KTP"
                                     class="ktp-preview">
                            </div>
                            <a href="{{ route('members.download-ktp', $member->id_member) }}"
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-download me-2"></i> Download KTP
                            </a>
                        @else
                            <p class="text-muted">Foto KTP belum diupload</p>
                        @endif

                        @if($member->ktp_verified_at)
                            <div class="alert alert-success mt-3">
                                <i class="fas fa-check-circle me-2"></i>
                                KTP terverifikasi pada {{ $member->ktp_verified_at->format('d F Y, H:i') }}
                            </div>
                        @else
                            <form action="{{ route('members.verify-ktp', $member->id_member) }}"
                                  method="POST" class="mt-3">
                                @csrf
                                <input type="hidden" name="verified" value="1">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-check me-2"></i> Verifikasi KTP
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Borrowing History -->
            @if(isset($member->borrowing) && $member->borrowing->count() > 0)
                <div class="member-card fade-in">
                    <div class="card-header-custom">
                        <h5><i class="fas fa-book-reader me-2"></i> Riwayat Peminjaman (10 Terakhir)</h5>
                    </div>
                    <div class="p-3">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Judul Buku</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Status</th>
                                    <th>Kondisi</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($member->borrowing as $index => $borrow)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $borrow->books->judul ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($borrow->pinjam)->format('d M Y') }}</td>
                                        <td>
                                            @if($borrow->status === 'dipinjam')
                                                <span class="badge bg-warning">Dipinjam</span>
                                            @elseif($borrow->status === 'kembali')
                                                <span class="badge bg-success">Kembali</span>
                                            @else
                                                <span class="badge bg-info">{{ ucfirst($borrow->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($borrow->kondisi === 'baik')
                                                <span class="badge bg-success">Baik</span>
                                            @else
                                                <span class="badge bg-danger">{{ ucfirst($borrow->kondisi) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL EDIT MEMBER -->
    <div class="modal fade" id="editMemberModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('members.update', $member->id_member) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="no_telp" class="form-control"
                                   value="{{ $member->no_telp }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2" required>{{ $member->alamat }}</textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 3D CARD -->
    @if($member->status === 'verified')
        <div class="modal fade" id="cardModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-card">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-id-card me-2"></i>Kartu Member - {{ $member->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="card-3d-container">
                            <div class="card-wrapper" id="card3D">
                                <!-- FRONT -->
                                <div class="card-face card-front">
                                    <div class="front-content">
                                        <div class="logo-area">
                                            <div class="logo">
                                                <i class="fas fa-book-open"></i>
                                            </div>
                                            <div class="library-name">
                                                <h2>BIMANTARA PUSTAKA</h2>
                                                <p>Member Card - Kartu Anggota</p>
                                            </div>
                                        </div>

                                        <div class="member-photo-wrapper">
                                            @if($member->photo_path)
                                                <img src="{{ route('members.download-photo', $member->id_member) }}" alt="{{ $member->name }}">
                                            @else
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&size=200&background=667eea&color=fff" alt="{{ $member->name }}">
                                            @endif
                                        </div>

                                        <div class="member-name-display">{{ strtoupper($member->name) }}</div>

                                        <div class="member-info-card">
                                            <div class="member-data-row">
                                                <span class="data-label">No. Anggota:</span>
                                                <span class="data-value">{{ ($member->tahun_pembuatan ?? date('Y')) . '-' . str_pad($member->id_member, 4, '0', STR_PAD_LEFT) }}</span>
                                            </div>
                                            <div class="member-data-row">
                                                <span class="data-label">No. Member:</span>
                                                <span class="data-value" style="font-family: 'Courier New', monospace; font-weight: 900;" id="memberCodeDisplay"></span>
                                            </div>
                                            <div class="member-data-row">
                                                <span class="data-label">TTL:</span>
                                                <span class="data-value">{{ $member->tempat_lahir }}, {{ \Carbon\Carbon::parse($member->tanggal_lahir)->format('d/m/Y') }}</span>
                                            </div>
                                            <div class="member-data-row">
                                                <span class="data-label">Jenis Kelamin:</span>
                                                <span class="data-value">{{ $member->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                                            </div>
                                            <div class="member-data-row">
                                                <span class="data-label">Alamat:</span>
                                                <span class="data-value">{{ Str::limit($member->alamat, 30) }}</span>
                                            </div>
                                            <div class="member-data-row">
                                                <span class="data-label">Institusi:</span>
                                                <span class="data-value">{{ Str::limit($member->institusi, 25) }}</span>
                                            </div>
                                        </div>

                                        <div class="barcode-section">
                                            <div class="barcode-container">
                                                <svg id="barcodeCanvas"></svg>
                                            </div>
                                            <div class="barcode-digits" id="barcodeDigits"></div>
                                            <div style="margin-top: 8px; font-size: 13px; font-weight: 700; color: #333; font-family: 'Courier New', monospace; letter-spacing: 2px;" id="barcodeText"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- BACK -->
                                <div class="card-face card-back">
                                    <div class="back-content">
                                        <div class="back-header">
                                            <h3>TATA TERTIB PEMINJAMAN</h3>
                                            <p>Library Membership Rules</p>
                                        </div>

                                        <div class="rules">
                                            <ol>
                                                <li>Kartu member harus dibawa setiap berkunjung ke perpustakaan</li>
                                                <li>Kartu member tidak boleh dipinjamkan atau digunakan oleh pihak lain</li>
                                                <li>Peminjaman maksimal 3 (tiga) buku dengan jangka waktu 1 minggu</li>
                                                <li>Perpanjangan peminjaman maksimal 1x dengan jangka waktu 1 minggu</li>
                                                <li>Perubahan alamat/nomor telepon harus segera dilaporkan</li>
                                                <li>Anggota wajib mematuhi segala peraturan yang berlaku</li>
                                                <li><strong>Jika menemukan kartu ini, harap dikembalikan</strong></li>
                                            </ol>
                                        </div>

                                        <div class="contact-info">
                                            <h4><i class="fas fa-headset"></i> HUBUNGI KAMI</h4>
                                            <div class="contact-item">📞 CS: 0271-123456</div>
                                            <div class="contact-item">✉️ info@bimantarapustaka.com</div>
                                            <div class="contact-item">📷 @bimantarapustaka</div>
                                            <div class="contact-item">📍 Jl. Perpustakaan No. 123, Surakarta</div>
                                        </div>

                                        <div class="found-notice">
                                            ⚠️ Kartu ini adalah milik Perpustakaan Bimantara Pustaka
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flip-hint">
                            <i class="fas fa-hand-pointer"></i> Klik kartu untuk flip
                        </div>

                        <div class="modal-card-actions">
                            <button type="button" class="btn btn-primary" onclick="flipCard()">
                                <i class="fas fa-sync-alt me-2"></i>Flip Card
                            </button>
                            <button type="button" class="btn btn-success" onclick="downloadCard()">
                                <i class="fas fa-download me-2"></i>Download Kartu
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.5/JsBarcode.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        let isFlipped = false;

        // Generate UPC Barcode dengan digit di bawah setiap garis
        function generateBarcode() {
            @php
                $gender = strtoupper(substr($member->gender ?? 'U', 0, 1));
                $day = \Carbon\Carbon::parse($member->created_at)->format('d');
                $month = \Carbon\Carbon::parse($member->created_at)->format('m');
                $counter = str_pad($member->id_member % 10000, 4, '0', STR_PAD_LEFT);

                // Convert to UPC-A compatible (12 digits)
                $genderCode = $gender === 'L' ? '1' : ($gender === 'P' ? '2' : '0');
                $barcodeData = '0' . $genderCode . $day . $month . $counter;

                // Ensure 11 digits before check digit calculation
                $barcodeData = str_pad(substr($barcodeData, 0, 11), 11, '0', STR_PAD_LEFT);
            @endphp

            const barcodeValue = '{{ $barcodeData }}';

            try {
                // Generate barcode
                JsBarcode("#barcodeCanvas", barcodeValue, {
                    format: "UPC",
                    width: 2,
                    height: 60,
                    displayValue: false,
                    margin: 0
                });

                // Display individual digits below barcode
                const digitsContainer = document.getElementById('barcodeDigits');
                digitsContainer.innerHTML = '';

                // Split barcode into individual digits
                const digits = barcodeValue.split('');
                digits.forEach(digit => {
                    const span = document.createElement('span');
                    span.className = 'barcode-digit';
                    span.textContent = digit;
                    digitsContainer.appendChild(span);
                });

            } catch (e) {
                console.error('Barcode generation error:', e);
            }
        }

        // Flip card function
        function flipCard() {
            const card = document.getElementById('card3D');
            isFlipped = !isFlipped;
            card.classList.toggle('flipped');
        }

        // Click on card to flip
        document.addEventListener('DOMContentLoaded', function() {
            const card = document.getElementById('card3D');
            if (card) {
                card.addEventListener('click', function(e) {
                    // Don't flip if clicking buttons
                    if (!e.target.closest('button')) {
                        flipCard();
                    }
                });
            }
        });

        // Generate barcode when modal opens
        document.getElementById('cardModal')?.addEventListener('shown.bs.modal', function () {
            generateBarcode();

            // Auto-flip demo
            setTimeout(() => {
                flipCard();
                setTimeout(() => flipCard(), 2500);
            }, 800);
        });

        // Reset flip when modal closes
        document.getElementById('cardModal')?.addEventListener('hidden.bs.modal', function () {
            if (isFlipped) {
                flipCard();
            }
        });

        // Download card as image
        async function downloadCard() {
            const card = document.getElementById('card3D');
            const originalFlip = isFlipped;

            // Show loading
            Swal.fire({
                title: 'Memproses...',
                html: 'Mengunduh kartu member...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const images = [];

                // Capture front
                if (isFlipped) flipCard();
                await new Promise(resolve => setTimeout(resolve, 200));
                const frontCanvas = await html2canvas(card, {
                    backgroundColor: null,
                    scale: 3,
                    logging: false,
                    useCORS: true
                });
                images.push(frontCanvas);

                // Capture back
                flipCard();
                await new Promise(resolve => setTimeout(resolve, 1000));
                const backCanvas = await html2canvas(card, {
                    backgroundColor: null,
                    scale: 3,
                    logging: false,
                    useCORS: true
                });
                images.push(backCanvas);

                // Create combined canvas
                const combinedCanvas = document.createElement('canvas');
                const ctx = combinedCanvas.getContext('2d');

                const padding = 60;
                const spacing = 100;

                combinedCanvas.width = Math.max(frontCanvas.width, backCanvas.width) + (padding * 2);
                combinedCanvas.height = frontCanvas.height + backCanvas.height + spacing + (padding * 2);

                // Background gradient
                const gradient = ctx.createLinearGradient(0, 0, 0, combinedCanvas.height);
                gradient.addColorStop(0, '#667eea');
                gradient.addColorStop(1, '#764ba2');
                ctx.fillStyle = gradient;
                ctx.fillRect(0, 0, combinedCanvas.width, combinedCanvas.height);

                // Add title
                ctx.fillStyle = 'white';
                ctx.font = 'bold 40px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('KARTU MEMBER - BIMANTARA PUSTAKA', combinedCanvas.width / 2, 50);

                // Add front card
                const frontX = (combinedCanvas.width - frontCanvas.width) / 2;
                ctx.drawImage(frontCanvas, frontX, padding + 60);

                // Add "DEPAN" label
                ctx.fillStyle = 'white';
                ctx.font = 'bold 30px Arial';
                ctx.fillText('DEPAN', combinedCanvas.width / 2, padding + 60 + frontCanvas.height + 40);

                // Add back card
                const backX = (combinedCanvas.width - backCanvas.width) / 2;
                ctx.drawImage(backCanvas, backX, padding + 60 + frontCanvas.height + spacing);

                // Add "BELAKANG" label
                ctx.fillText('BELAKANG', combinedCanvas.width / 2, padding + 60 + frontCanvas.height + spacing + backCanvas.height + 40);

                // Download
                combinedCanvas.toBlob(blob => {
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'kartu-member-{{ strtolower(str_replace(" ", "-", $member->name)) }}.png';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Kartu member berhasil diunduh',
                        timer: 2000,
                        showConfirmButton: false
                    });
                });

                // Restore original state
                if (originalFlip !== isFlipped) {
                    await new Promise(resolve => setTimeout(resolve, 200));
                    flipCard();
                }

            } catch (error) {
                console.error('Download error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat mengunduh kartu'
                });
            }
        }

        // Success/Error alerts
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
            title: 'Gagal!',
            text: '{{ session("error") }}',
            timer: 3000,
            showConfirmButton: false
        });
        @endif
    </script>
@endpush
