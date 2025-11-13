<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Member - {{ $member->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #38a169;
            --warning-color: #dd6b20;
            --danger-color: #e53e3e;
            --bg-color: #f5f7fa;
            --card-bg: #ffffff;
            --text-dark: #2d3748;
            --text-muted: #718096;
            --border-color: #e2e8f0;
        }

        body {
            background: var(--bg-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 3rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .page-header h1 {
            color: white;
            font-weight: 700;
            margin: 0;
        }

        .page-header .breadcrumb {
            background: transparent;
            margin: 0;
            padding: 0;
        }

        .page-header .breadcrumb-item a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
        }

        .page-header .breadcrumb-item.active {
            color: white;
        }

        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .profile-card {
            background: var(--card-bg);
            border-radius: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border: 1px solid var(--border-color);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 2rem;
            text-align: center;
            position: relative;
        }

        .profile-photo-wrapper {
            width: 150px;
            height: 150px;
            margin: 0 auto 1rem;
            position: relative;
        }

        .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            object-fit: cover;
        }

        .status-badge {
            position: absolute;
            bottom: 10px;
            right: 10px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 3px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .status-badge.verified {
            background: var(--success-color);
            color: white;
        }

        .status-badge.pending {
            background: var(--warning-color);
            color: white;
        }

        .status-badge.rejected {
            background: var(--danger-color);
            color: white;
        }

        .profile-name {
            color: white;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .profile-email {
            color: rgba(255,255,255,0.9);
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        .verification-code-box {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border-radius: 0.75rem;
            padding: 1rem;
            display: inline-block;
            margin-top: 1rem;
        }

        .verification-code {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 0.5rem;
            color: white;
        }

        .card-section {
            padding: 2rem;
        }

        .card-section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-section-title i {
            color: var(--primary-color);
        }

        .info-row {
            display: flex;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: var(--text-muted);
            width: 200px;
            flex-shrink: 0;
        }

        .info-value {
            color: var(--text-dark);
            flex: 1;
        }

        .status-chip {
            display: inline-block;
            padding: 0.35rem 1rem;
            border-radius: 2rem;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .status-chip.verified {
            background: #f0fff4;
            color: var(--success-color);
        }

        .status-chip.pending {
            background: #fffaf0;
            color: var(--warning-color);
        }

        .status-chip.rejected {
            background: #fff5f5;
            color: var(--danger-color);
        }

        .borrowing-table {
            width: 100%;
            margin-top: 1rem;
        }

        .borrowing-table th {
            background: #f8f9fa;
            font-weight: 600;
            padding: 0.75rem;
            border-bottom: 2px solid var(--border-color);
        }

        .borrowing-table td {
            padding: 0.75rem;
            border-bottom: 1px solid var(--border-color);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        .btn-custom {
            padding: 0.625rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-outline-custom {
            border: 2px solid var(--border-color);
            color: var(--text-dark);
            background: white;
        }

        .btn-outline-custom:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.5rem;
            }

            .profile-photo-wrapper,
            .profile-photo {
                width: 120px;
                height: 120px;
            }

            .info-row {
                flex-direction: column;
                gap: 0.5rem;
            }

            .info-label {
                width: 100%;
            }

            .borrowing-table {
                font-size: 0.875rem;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }
    </style>
</head>
<body>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-user-circle me-2"></i> Profil Member</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Profil Member</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container profile-container mb-5">

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

    <!-- Profile Card -->
    <div class="profile-card fade-in">
        <div class="profile-header">
            <div class="profile-photo-wrapper">
                @php
                    $photoUrl = null;
                    if ($member->photo_path && file_exists(public_path('storage/' . $member->photo_path))) {
                        $photoUrl = asset('storage/' . $member->photo_path);
                    }
                @endphp

                @if($photoUrl)
                    <img src="{{ $photoUrl }}" class="profile-photo" alt="{{ $member->name }}">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&size=150&background=4a5568&color=fff"
                         class="profile-photo"
                         alt="{{ $member->name }}">
                @endif



                <!-- Status Badge -->
                @if($member->status === 'verified')
                    <div class="status-badge verified" title="Terverifikasi">
                        <i class="fas fa-check"></i>
                    </div>
                @elseif($member->status === 'pending')
                    <div class="status-badge pending" title="Pending">
                        <i class="fas fa-clock"></i>
                    </div>
                @else
                    <div class="status-badge rejected" title="Ditolak">
                        <i class="fas fa-times"></i>
                    </div>
                @endif
            </div>

            <div class="profile-name">{{ $member->name }}</div>
            <div class="profile-email">
                <i class="fas fa-envelope me-2"></i>{{ $member->email }}
            </div>

            <!-- Verification Code (jika sudah verified) -->
            @if($member->status === 'verified' && $member->verification_code)
                <div class="verification-code-box">
                    <small style="color: rgba(255,255,255,0.8);">Kode Verifikasi:</small>
                    <div class="verification-code">{{ $member->verification_code }}</div>
                </div>
            @endif
        </div>

        <!-- Informasi Pribadi -->
        <div class="card-section">
            <h3 class="card-section-title">
                <i class="fas fa-user"></i>
                Informasi Pribadi
            </h3>

            <div class="info-row">
                <div class="info-label">ID Member:</div>
                <div class="info-value"><strong>#{{ str_pad($member->id_member, 6, '0', STR_PAD_LEFT) }}</strong></div>
            </div>

            <div class="info-row">
                <div class="info-label">Nama Lengkap:</div>
                <div class="info-value">{{ $member->name }}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $member->email }}</div>
            </div>

            <div class="info-row">
                <div class="info-label">No. Telepon:</div>
                <div class="info-value">{{ $member->no_telp }}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Alamat:</div>
                <div class="info-value">{{ $member->alamat }}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Status Member:</div>
                <div class="info-value">
                    @if($member->status === 'verified')
                        <span class="status-chip verified">
                            <i class="fas fa-check-circle me-1"></i> Terverifikasi
                        </span>
                    @elseif($member->status === 'pending')
                        <span class="status-chip pending">
                            <i class="fas fa-clock me-1"></i> Menunggu Verifikasi
                        </span>
                    @else
                        <span class="status-chip rejected">
                            <i class="fas fa-times-circle me-1"></i> Ditolak
                        </span>
                    @endif
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">Tanggal Daftar:</div>
                <div class="info-value">{{ $member->created_at->format('d F Y, H:i') }} WIB</div>
            </div>

            @if($member->admin_verified_at)
                <div class="info-row">
                    <div class="info-label">Diverifikasi Pada:</div>
                    <div class="info-value">{{ $member->admin_verified_at->format('d F Y, H:i') }} WIB</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Riwayat Peminjaman -->
    <div class="profile-card fade-in">
        <div class="card-section">
            <h3 class="card-section-title">
                <i class="fas fa-book-reader"></i>
                Riwayat Peminjaman (5 Terakhir)
            </h3>

            @if($member->borrowing && $member->borrowing->count() > 0)
                <div class="table-responsive">
                    <table class="borrowing-table">
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
                                <td>
                                    <strong>{{ $borrow->books->judul ?? 'N/A' }}</strong>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($borrow->pinjam)->format('d M Y') }}</td>
                                <td>
                                    @if($borrow->status === 'dipinjam')
                                        <span class="badge bg-warning">Dipinjam</span>
                                    @elseif($borrow->status === 'kembali')
                                        <span class="badge bg-success">Dikembalikan</span>
                                    @else
                                        <span class="badge bg-info">{{ ucfirst($borrow->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($borrow->kondisi === 'baik')
                                        <span class="badge bg-success">Baik</span>
                                    @elseif($borrow->kondisi === 'rusak')
                                        <span class="badge bg-danger">Rusak</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($borrow->kondisi) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 text-center">
                    <a href="{{ route('borrowing.index') }}" class="btn btn-outline-custom btn-custom">
                        <i class="fas fa-list me-2"></i> Lihat Semua Riwayat
                    </a>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-book-open"></i>
                    <p>Belum ada riwayat peminjaman</p>
                    <a href="{{ route('books.index') }}" class="btn btn-primary-custom btn-custom mt-3">
                        <i class="fas fa-search me-2"></i> Cari Buku
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="text-center mt-4 fade-in">
        <a href="{{ route('members.edit', $member->id_member) }}" class="btn btn-primary-custom btn-custom me-2">
            <i class="fas fa-edit me-2"></i> Edit Profil
        </a>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-custom btn-custom">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
        </a>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
