<!DOCTYPE html>
@php
    $member = \App\Models\Members::where('id_user', Auth::id())->first();
@endphp
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Konsumen - Bimantara Pustaka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        :root {
            --bg-color: #f5f7fa;
            --card-bg: #ffffff;
            --text-colox`: #2d3748;
            --subtext-color: #718096;
            --border-color: #e2e8f0;
            --shadow-light: rgba(0, 0, 0, 0.06);
            --shadow-medium: rgba(0, 0, 0, 0.08);
            --shadow-hover: rgba(0, 0, 0, 0.12);
            --accent-blue: #3182ce;
            --accent-green: #38a169;
            --accent-orange: #dd6b20;
            --accent-purple: #805ad5;
            --accent-red: #e53e3e;
            --accent-teal: #319795;
            --transition-time: 0.2s ease;
            --font-base: 1rem;
            --font-small: 0.875rem;
            --font-large: 1.75rem;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--bg-color);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-color);
            font-size: var(--font-base);
        }

        .top-header {
            background: var(--card-bg);
            padding: 1rem 0;
            box-shadow: 0 2px 8px var(--shadow-light);
            border-bottom: 1px solid var(--border-color);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .library-title {
            font-size: 1.375rem;
            font-weight: 700;
            margin: 0;
            color: var(--text-color);
        }

        .library-title i {
            color: var(--subtext-color);
        }

        .header-clock {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
        }

        .header-clock-time {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .header-clock-date {
            font-size: 0.75rem;
            color: var(--subtext-color);
        }

        .profile-section {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .profile-photo {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            border: 2px solid var(--border-color);
            object-fit: cover;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
            text-align: right;
        }

        .profile-name {
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-color);
        }

        .profile-role {
            font-size: 0.75rem;
            color: var(--subtext-color);
        }

        .content-wrapper {
            padding: 2rem 0;
        }

        .welcome-section {
            background: var(--card-bg);
            border-radius: 0.75rem;
            padding: 2rem;
            margin: 1.5rem 0;
            box-shadow: 0 1px 3px var(--shadow-medium);
            border: 1px solid var(--border-color);
        }

        .welcome-section h2 {
            font-size: var(--font-large);
            font-weight: 700;
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }

        .welcome-section p {
            font-size: 0.9375rem;
            color: var(--subtext-color);
            margin: 0;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(15rem, 1fr));
            gap: 1.25rem;
            margin: 2rem 0;
        }

        .menu-card {
            background: var(--card-bg);
            border-radius: 0.625rem;
            padding: 1.75rem 1.5rem;
            text-align: center;
            box-shadow: 0 1px 3px var(--shadow-medium);
            border: 1px solid var(--border-color);
            transition: all var(--transition-time);
            cursor: pointer;
            text-decoration: none;
            display: block;
        }

        .menu-card:hover {
            transform: translateY(-0.25rem);
            box-shadow: 0 4px 12px var(--shadow-hover);
            border-color: #cbd5e0;
        }

        .menu-icon {
            width: 4rem;
            height: 4rem;
            margin: 0 auto 1.25rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            transition: all var(--transition-time);
        }

        .menu-card:hover .menu-icon {
            transform: scale(1.05);
        }

        .menu-card.blue .menu-icon {
            background: #ebf8ff;
            color: var(--accent-blue);
        }

        .menu-card.green .menu-icon {
            background: #f0fff4;
            color: var(--accent-green);
        }

        .menu-card.orange .menu-icon {
            background: #fffaf0;
            color: var(--accent-orange);
        }

        .menu-card.purple .menu-icon {
            background: #faf5ff;
            color: var(--accent-purple);
        }

        .menu-card.red .menu-icon {
            background: #fff5f5;
            color: var(--accent-red);
        }

        .menu-card.teal .menu-icon {
            background: #e6fffa;
            color: var(--accent-teal);
        }

        .menu-title {
            font-size: 1.0625rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }

        .menu-description {
            font-size: var(--font-small);
            color: var(--subtext-color);
            line-height: 1.5;
        }

        .alert {
            border-radius: 0.75rem;
            border: none;
            box-shadow: 0 1px 3px var(--shadow-medium);
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #f0fff4;
            color: #22543d;
            border-left: 4px solid #38a169;
        }

        .alert-warning {
            background: #fffaf0;
            color: #744210;
            border-left: 4px solid #dd6b20;
        }

        .alert-info {
            background: #ebf8ff;
            color: #2c5282;
            border-left: 4px solid #3182ce;
        }

        .alert-danger {
            background: #fff5f5;
            color: #742a2a;
            border-left: 4px solid #e53e3e;
        }

        .verification-code-box {
            margin-top: 1rem;
            padding: 1.25rem;
            background: #ffffff;
            border-radius: 0.5rem;
            border: 2px dashed #38a169;
        }

        .verification-code-box strong {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }

        .verification-code-display {
            font-size: 2rem;
            font-weight: 700;
            color: #38a169;
            letter-spacing: 0.5rem;
            text-align: center;
            margin: 0.75rem 0;
        }

        .verification-code-box small {
            display: block;
            text-align: center;
            color: var(--subtext-color);
            margin-top: 0.5rem;
        }

        .profile-dropdown {
            background: var(--card-bg);
            border-radius: 0.625rem;
            box-shadow: 0 4px 12px var(--shadow-hover);
            border: 1px solid var(--border-color);
            padding: 0.5rem;
        }

        .profile-dropdown .dropdown-item {
            border-radius: 0.375rem;
            padding: 0.625rem 1rem;
            margin: 0.25rem 0;
            transition: all var(--transition-time);
            font-size: var(--font-small);
        }

        .profile-dropdown .dropdown-item:hover {
            background: #f7fafc;
        }

        .profile-dropdown .dropdown-item.text-danger:hover {
            background: #fff5f5;
        }

        .btn-profile {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--subtext-color);
            font-weight: 500;
            padding: 0.625rem 1.5rem;
            border-radius: 0.5rem;
            font-size: 0.9375rem;
            transition: all var(--transition-time);
        }

        .btn-profile:hover {
            background: #f7fafc;
            border-color: var(--subtext-color);
            color: var(--text-color);
        }

        @media (max-width: 768px) {
            .library-title {
                font-size: 1.125rem;
            }

            .header-clock {
                display: none;
            }

            .welcome-section {
                padding: 1.5rem 1.25rem;
            }

            .welcome-section h2 {
                font-size: 1.375rem;
            }

            .menu-grid {
                grid-template-columns: 1fr;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.4s ease forwards;
        }
    </style>
</head>
<body>

<div class="top-header">
    <div class="container">
        <div class="header-content">
            <h1 class="library-title">
                <i class="fas fa-book-open me-2"></i>
                Bimantara Pustaka
            </h1>

            <div class="header-clock">
                <div class="header-clock-time" id="header-time">00:00:00</div>
                <div class="header-clock-date" id="header-date">Loading...</div>
            </div>

            <div class="profile-section">
                @if(Auth::user()->photo)
                    <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="Profile" class="profile-photo">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4a5568&color=fff&size=40" alt="Profile" class="profile-photo">
                @endif
                <div class="profile-info">
                    <div class="profile-name">{{ Auth::user()->name ?? 'User' }}</div>
                    <div class="profile-role">{{ ucfirst(Auth::user()->role ?? 'Konsumen') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-wrapper">
    <div class="container">
        <div class="welcome-section fade-in">
            <h2>Selamat Datang di Dashboard Konsumen, {{ Auth::user()->name ?? 'User' }}! 👋</h2>
            <p>Kelola perpustakaan digital dengan mudah, cepat, dan terorganisir</p>
        </div>

        {{-- SUCCESS MESSAGE --}}
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

        {{-- ✅ MEMBER STATUS CHECK (DENGAN NULL SAFETY) --}}
        @if(!isset($member) || is_null($member))
            <div class="alert alert-warning fade-in" role="alert">
                <h5 class="alert-heading">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Anda Belum Terdaftar Sebagai Member
                </h5>
                <p class="mb-3">Daftar sebagai member untuk dapat meminjam buku di perpustakaan.</p>
                <a href="{{ route('members.create') }}" class="btn btn-warning">
                    <i class="fas fa-user-plus me-2"></i> Daftar Member Sekarang
                </a>
            </div>
        @elseif($member->status === 'pending')
            <div class="alert alert-warning fade-in" role="alert">
                <h5 class="alert-heading">
                    <i class="fas fa-clock me-2"></i>
                    Data Member Anda Sedang Diproses
                </h5>
                <p class="mb-0">Admin sedang memverifikasi data Anda. Kami akan mengirim email setelah proses verifikasi selesai.</p>
                <hr>
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Biasanya proses verifikasi memakan waktu 1x24 jam.
                </small>
            </div>
        @elseif($member->status === 'verified' || $member->status === 'active')
            <div class="alert alert-success fade-in" role="alert">
                <h5 class="alert-heading">
                    <i class="fas fa-check-circle me-2"></i>
                    Member Terverifikasi ✅
                </h5>
                <p class="mb-2">Selamat! Akun member Anda sudah diverifikasi dan dapat digunakan untuk meminjam buku.</p>
                @if(isset($member->verification_code) && $member->verification_code)
                    <div class="verification-code-box">
                        <strong>Kode Verifikasi Anda:</strong>
                        <div class="verification-code-display">
                            {{ $member->verification_code }}
                        </div>
                        <small class="text-muted">Simpan kode ini untuk keperluan peminjaman buku</small>
                    </div>
                @endif
            </div>
        @elseif($member->status === 'rejected')
            <div class="alert alert-danger fade-in" role="alert">
                <h5 class="alert-heading">
                    <i class="fas fa-times-circle me-2"></i>
                    Pendaftaran Ditolak
                </h5>
                <p class="mb-0">Mohon maaf, pendaftaran Anda ditolak oleh admin. Silakan hubungi perpustakaan untuk informasi lebih lanjut.</p>
            </div>
        @elseif(in_array($member->status, ['inactive', 'suspended']))
            <div class="alert alert-danger fade-in" role="alert">
                <h5 class="alert-heading">
                    <i class="fas fa-ban me-2"></i>
                    Akun Member Tidak Aktif
                </h5>
                <p class="mb-0">Akun member Anda sedang tidak aktif. Hubungi admin untuk informasi lebih lanjut.</p>
            </div>
        @endif

        <div class="menu-grid">
            <a href="{{ route('books.index') }}" class="menu-card blue fade-in">
                <div class="menu-icon"><i class="fa fa-book"></i></div>
                <div class="menu-title">Buku</div>
                <div class="menu-description">Lihat koleksi buku perpustakaan</div>
            </a>

            <a href="{{ route('members.index') }}" class="menu-card purple fade-in">
                <div class="menu-icon"><i class="fa fa-id-card"></i></div>
                <div class="menu-title">Profil Member</div>
                <div class="menu-description">Lihat data member Anda</div>
            </a>

            <a href="{{ route('borrowing.index') }}" class="menu-card orange fade-in">
                <div class="menu-icon"><i class="fa fa-book-reader"></i></div>
                <div class="menu-title">Peminjaman</div>
                <div class="menu-description">Riwayat peminjaman buku</div>
            </a>

            <a href="{{ route('racks.index') }}" class="menu-card green fade-in">
                <div class="menu-icon"><i class="fa fa-archive"></i></div>
                <div class="menu-title">Rak Buku</div>
                <div class="menu-description">Daftar rak penyimpanan buku</div>
            </a>

            <a href="{{ route('rackslocation.index') }}" class="menu-card teal fade-in">
                <div class="menu-icon"><i class="fa fa-map-marker-alt"></i></div>
                <div class="menu-title">Lokasi Rak</div>
                <div class="menu-description">Lokasi tiap rak buku</div>
            </a>

            <a href="{{ route('publisher.index') }}" class="menu-card red fade-in">
                <div class="menu-icon"><i class="fa fa-building"></i></div>
                <div class="menu-title">Penerbit</div>
                <div class="menu-description">Informasi penerbit buku</div>
            </a>

            <a href="{{ route('categories.index') }}" class="menu-card blue fade-in">
                <div class="menu-icon"><i class="fa fa-folder"></i></div>
                <div class="menu-title">Kategori</div>
                <div class="menu-description">Kategori buku tersedia</div>
            </a>

            <a href="{{ route('subcategories.index') }}" class="menu-card green fade-in">
                <div class="menu-icon"><i class="fa fa-layer-group"></i></div>
                <div class="menu-title">Subkategori</div>
                <div class="menu-description">Sub kategori buku</div>
            </a>
        </div>

        <div class="text-center mb-5 fade-in">
            <div class="dropdown d-inline-block">
                <button class="btn btn-profile dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fa fa-user me-2"></i>{{ Auth::user()->name ?? 'Profile' }}
                </button>
                <ul class="dropdown-menu profile-dropdown">
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="fa fa-user-edit me-2"></i> Edit Profil
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fa fa-sign-out-alt me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Real-time Clock Function
    function updateClock() {
        const now = new Date();

        // Format time (HH:MM:SS)
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('header-time').textContent = `${hours}:${minutes}:${seconds}`;

        // Format date (Day, DD Month YYYY)
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const dateString = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
        document.getElementById('header-date').textContent = dateString;
    }

    // Initialize clock
    updateClock();
    setInterval(updateClock, 1000);

    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
</body>
</html>
