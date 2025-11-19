<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Bimantara Pustaka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f5f7fa;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #2d3748;
        }

        .top-header {
            background: #ffffff;
            padding: 16px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border-bottom: 1px solid #e2e8f0;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .library-title {
            font-size: 22px;
            font-weight: 700;
            margin: 0;
            color: #2d3748;
        }

        .library-title i {
            color: #4a5568;
        }

        .header-clock {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
        }

        .header-clock-time {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
        }

        .header-clock-date {
            font-size: 12px;
            color: #718096;
        }

        .profile-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-outline-custom {
            border: 1px solid #cbd5e0;
            color: #4a5568;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.2s ease;
            font-size: 14px;
            background: #ffffff;
        }

        .btn-outline-custom:hover {
            background: #f7fafc;
            border-color: #4a5568;
            color: #2d3748;
        }

        .profile-photo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #e2e8f0;
            object-fit: cover;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
            text-align: right;
        }

        .profile-name {
            font-weight: 600;
            font-size: 14px;
            color: #2d3748;
        }

        .profile-role {
            font-size: 12px;
            color: #718096;
        }

        .content-wrapper {
            padding: 32px 0;
        }

        .welcome-section {
            background: #ffffff;
            border-radius: 12px;
            padding: 32px;
            margin: 24px 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }

        .welcome-section h2 {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 8px;
        }

        .welcome-section p {
            font-size: 15px;
            color: #718096;
            margin: 0;
        }

        /* Statistics Cards */
        .stat-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15) !important;
        }

        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.3;
        }

        /* Quick Access Section */
        .quick-access-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            margin-bottom: 24px;
        }

        .quick-access-btn {
            transition: all 0.2s ease;
        }

        .quick-access-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin: 32px 0;
        }

        .menu-card {
            background: #ffffff;
            border-radius: 10px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .menu-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            border-color: #cbd5e0;
        }

        .menu-icon {
            width: 56px;
            height: 56px;
            flex-shrink: 0;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            transition: all 0.2s ease;
        }

        .menu-card:hover .menu-icon {
            transform: scale(1.05);
        }

        .menu-card.blue .menu-icon {
            background: #ebf8ff;
            color: #3182ce;
        }

        .menu-card.green .menu-icon {
            background: #f0fff4;
            color: #38a169;
        }

        .menu-card.orange .menu-icon {
            background: #fffaf0;
            color: #dd6b20;
        }

        .menu-card.purple .menu-icon {
            background: #faf5ff;
            color: #805ad5;
        }

        .menu-card.red .menu-icon {
            background: #fff5f5;
            color: #e53e3e;
        }

        .menu-card.teal .menu-icon {
            background: #e6fffa;
            color: #319795;
        }

        .menu-card.indigo .menu-icon {
            background: #ebf4ff;
            color: #5a67d8;
        }

        .menu-card.pink .menu-icon {
            background: #fff5f7;
            color: #d53f8c;
        }

        .menu-card.cyan .menu-icon {
            background: #e0f7ff;
            color: #0891b2;
        }

        .menu-card.yellow .menu-icon {
            background: #fffbeb;
            color: #d97706;
        }

        .menu-title {
            font-size: 16px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 4px;
        }

        .menu-description {
            font-size: 13px;
            color: #718096;
            line-height: 1.4;
        }

        .menu-content {
            flex: 1;
        }

        .about-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            margin: 32px 0;
        }

        .about-card h5 {
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 16px;
        }

        .about-card p {
            font-size: 14px;
            line-height: 1.7;
            color: #4a5568;
            margin-bottom: 12px;
        }

        .about-card p:last-child {
            margin-bottom: 0;
        }

        .profile-dropdown {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            padding: 8px;
        }

        .profile-dropdown .dropdown-item {
            border-radius: 6px;
            padding: 10px 16px;
            margin: 4px 0;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .profile-dropdown .dropdown-item:hover {
            background: #f7fafc;
        }

        .profile-dropdown .dropdown-item.text-danger:hover {
            background: #fff5f5;
        }

        .btn-profile {
            background: #ffffff;
            border: 1px solid #cbd5e0;
            color: #4a5568;
            font-weight: 500;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.2s ease;
        }

        .btn-profile:hover {
            background: #f7fafc;
            border-color: #4a5568;
            color: #2d3748;
        }

        .alert {
            border-radius: 12px;
            border: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .alert-success {
            background: #f0fff4;
            color: #22543d;
            border-left: 4px solid #38a169;
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

        .alert .btn {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
        }

        .alert ul {
            list-style: none;
            padding-left: 0;
        }

        .alert ul li {
            padding: 0.5rem 0;
        }

        @media (max-width: 768px) {
            .library-title {
                font-size: 18px;
            }

            .header-clock {
                display: none;
            }

            .welcome-section {
                padding: 24px 20px;
            }

            .welcome-section h2 {
                font-size: 22px;
            }

            .menu-grid {
                grid-template-columns: 1fr;
            }

            .menu-card {
                flex-direction: column;
                text-align: center;
            }

            .menu-content {
                text-align: center;
            }

            .btn-outline-custom span {
                display: none;
            }

            .btn-outline-custom {
                padding: 8px 12px;
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
                <a href="{{ route('users.index') }}" class="btn btn-outline-custom">
                    <i class="fa fa-users-cog me-1"></i>
                    <span>Kelola User</span>
                </a>
                @if(Auth::user()->photo)
                    <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="Profile" class="profile-photo">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4a5568&color=fff&size=40" alt="Profile" class="profile-photo">
                @endif
                <div class="profile-info">
                    <div class="profile-name">{{ Auth::user()->name ?? 'Administrator' }}</div>
                    <div class="profile-role">{{ Auth::user()->role ?? 'Admin' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-wrapper">
    <div class="container">
        <div class="welcome-section fade-in">
            <h2>Selamat Datang, {{ Auth::user()->name ?? 'User' }}!</h2>
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

        {{-- ALERT KETERLAMBATAN KRITIS --}}
        @php
            $terlambat = \App\Models\Borrowing::with(['users', 'member', 'books'])
                ->whereIn('status', ['Dipinjam', 'dipinjam'])
                ->whereDate('pengembalian', '<', now())
                ->get();

            $terlambatBerat = $terlambat->filter(function($b) {
                return $b->getDaysLate() > 7;
            });
        @endphp

        @if($terlambatBerat->count() > 0)
            <div class="alert alert-danger alert-dismissible fade show fade-in" role="alert">
                <h5 class="alert-heading">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Peringatan! Keterlambatan Kritis
                </h5>
                <p class="mb-2">Terdapat <strong>{{ $terlambatBerat->count() }} peminjaman</strong> yang terlambat lebih dari 7 hari!</p>
                <hr>
                <div class="d-flex gap-2">
                    <a href="{{ route('laporan.keterlambatan') }}" class="btn btn-sm btn-light">
                        <i class="fas fa-eye"></i> Lihat Detail
                    </a>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        {{-- NOTIFIKASI MEMBER BARU --}}
        @php
            $unreadNotifications = auth()->user()->unreadNotifications()->where('type', 'App\Notifications\NewMemberRegistered')->get();
        @endphp

        @if($unreadNotifications->count() > 0)
            <div class="alert alert-info alert-dismissible fade show fade-in" role="alert">
                <h5 class="alert-heading">
                    <i class="fas fa-bell me-2"></i>
                    Notifikasi Member Baru ({{ $unreadNotifications->count() }})
                </h5>
                <hr>
                <ul class="mb-0">
                    @foreach($unreadNotifications as $notification)
                        <li class="mb-2">
                            {{ $notification->data['message'] ?? 'Member baru menunggu verifikasi' }}
                            <a href="{{ $notification->data['action_url'] ?? route('members.index') }}" class="btn btn-sm btn-primary ms-2">
                                <i class="fas fa-check me-1"></i> Verifikasi Sekarang
                            </a>
                            <form method="POST" action="{{ route('notifications.mark-read', $notification->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-secondary ms-1">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- QUICK ACCESS LAPORAN --}}
        <div class="quick-access-card fade-in">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Access - Laporan & Monitoring</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="{{ route('laporan.denda') }}" class="btn btn-outline-danger w-100 py-3 quick-access-btn">
                            <i class="fas fa-money-bill-wave fa-2x d-block mb-2"></i>
                            <strong>Daftar Denda</strong>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('laporan.riwayat') }}" class="btn btn-outline-primary w-100 py-3 quick-access-btn">
                            <i class="fas fa-history fa-2x d-block mb-2"></i>
                            <strong>Riwayat Transaksi</strong>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('laporan.keterlambatan') }}" class="btn btn-outline-warning w-100 py-3 quick-access-btn">
                            <i class="fas fa-exclamation-triangle fa-2x d-block mb-2"></i>
                            <strong>Keterlambatan</strong>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('laporan.buku-rusak') }}" class="btn btn-outline-secondary w-100 py-3 quick-access-btn">
                            <i class="fas fa-tools fa-2x d-block mb-2"></i>
                            <strong>Buku Rusak</strong>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- STATISTICS WIDGETS --}}
        @php
            $totalBuku = \App\Models\Books::count();
            $totalPeminjaman = \App\Models\Borrowing::whereMonth('pinjam', now()->month)->count();
            $totalMember = \App\Models\Members::where('status', 'verified')->count();
            $bukuTersedia = \App\Models\Bookitems::where('status', 'tersedia')->count();
        @endphp

        <div class="row mb-4 fade-in">
            <div class="col-md-3">
                <div class="card stat-card border-primary shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Buku</h6>
                                <h3 class="mb-0 text-primary">{{ $totalBuku }}</h3>
                            </div>
                            <div class="stat-icon text-primary">
                                <i class="fas fa-book"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card border-success shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Peminjaman Bulan Ini</h6>
                                <h3 class="mb-0 text-success">{{ $totalPeminjaman }}</h3>
                            </div>
                            <div class="stat-icon text-success">
                                <i class="fas fa-handshake"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card border-info shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Member Aktif</h6>
                                <h3 class="mb-0 text-info">{{ $totalMember }}</h3>
                            </div>
                            <div class="stat-icon text-info">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card border-warning shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Buku Tersedia</h6>
                                <h3 class="mb-0 text-warning">{{ $bukuTersedia }}</h3>
                            </div>
                            <div class="stat-icon text-warning">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="menu-grid">
            <a href="{{ route('books.index') }}" class="menu-card blue fade-in">
                <div class="menu-icon"><i class="fa fa-book"></i></div>
                <div class="menu-content">
                    <div class="menu-title">Buku</div>
                    <div class="menu-description">Kelola koleksi buku perpustakaan</div>
                </div>
            </a>

            <a href="{{ route('members.index') }}" class="menu-card purple fade-in">
                <div class="menu-icon"><i class="fa fa-users"></i></div>
                <div class="menu-content">
                    <div class="menu-title">Members</div>
                    <div class="menu-description">Kelola member perpustakaan</div>
                </div>
            </a>

            <a href="{{ route('racks.index') }}" class="menu-card green fade-in">
                <div class="menu-icon"><i class="fa fa-archive"></i></div>
                <div class="menu-content">
                    <div class="menu-title">Rak</div>
                    <div class="menu-description">Daftar rak penyimpanan buku</div>
                </div>
            </a>

            <a href="{{ route('rackslocation.index') }}" class="menu-card orange fade-in">
                <div class="menu-icon"><i class="fa fa-map-marker-alt"></i></div>
                <div class="menu-content">
                    <div class="menu-title">Lokasi Rak</div>
                    <div class="menu-description">Lokasi tiap rak buku</div>
                </div>
            </a>

            <a href="{{ route('publisher.index') }}" class="menu-card purple fade-in">
                <div class="menu-icon"><i class="fa fa-building"></i></div>
                <div class="menu-content">
                    <div class="menu-title">Penerbit</div>
                    <div class="menu-description">Manajemen penerbit</div>
                </div>
            </a>

            <a href="{{ route('categories.index') }}" class="menu-card red fade-in">
                <div class="menu-icon"><i class="fa fa-folder"></i></div>
                <div class="menu-content">
                    <div class="menu-title">Kategori</div>
                    <div class="menu-description">Atur kategori buku</div>
                </div>
            </a>

            <a href="{{ route('subcategories.index') }}" class="menu-card teal fade-in">
                <div class="menu-icon"><i class="fa fa-layer-group"></i></div>
                <div class="menu-content">
                    <div class="menu-title">Subkategori</div>
                    <div class="menu-description">Kelola sub kategori</div>
                </div>
            </a>

            <a href="{{ route('sortbooks.index') }}" class="menu-card cyan fade-in">
                <div class="menu-icon"><i class="fa fa-sort-amount-down"></i></div>
                <div class="menu-content">
                    <div class="menu-title">Penataan</div>
                    <div class="menu-description">Atur penataan buku di rak</div>
                </div>
            </a>

            <a href="{{ route('borrowing.index') }}" class="menu-card yellow fade-in">
                <div class="menu-icon"><i class="fa fa-handshake"></i></div>
                <div class="menu-content">
                    <div class="menu-title">Peminjaman</div>
                    <div class="menu-description">Kelola transaksi peminjaman buku</div>
                </div>
            </a>
        </div>

        <div class="about-card fade-in">
            <h5><i class="fa fa-info-circle me-2"></i> Tentang Kami</h5>
            <p>
                Bimantara Pustaka adalah sistem perpustakaan digital modern yang memudahkan pengelolaan buku,
                kategori, subkategori, penerbit, hingga manajemen pengguna.
            </p>
            <p>
                Dengan teknologi terkini, kami berkomitmen untuk menciptakan akses literasi yang mudah, cepat,
                dan terorganisir bagi semua kalangan.
            </p>
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
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('header-time').textContent = `${hours}:${minutes}:${seconds}`;

        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const dateString = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
        document.getElementById('header-date').textContent = dateString;
    }

    updateClock();
    setInterval(updateClock, 1000);
</script>
</body>
</html>
