<!DOCTYPE html>
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
            --text-color: #2d3748;
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

        .btn-outline-custom {
            border: 1px solid var(--border-color);
            color: var(--subtext-color);
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            transition: all var(--transition-time);
            font-size: 0.875rem;
            background: var(--card-bg);
        }

        .btn-outline-custom:hover {
            background: #f7fafc;
            border-color: var(--subtext-color);
            color: var(--text-color);
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

        .about-card {
            background: var(--card-bg);
            border-radius: 0.75rem;
            padding: 2rem;
            box-shadow: 0 1px 3px var(--shadow-medium);
            border: 1px solid var(--border-color);
            margin: 2rem 0;
        }

        .about-card h5 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-color);
            margin-bottom: 1rem;
        }

        .about-card p {
            font-size: var(--font-small);
            line-height: 1.7;
            color: var(--subtext-color);
            margin-bottom: 0.75rem;
        }

        .about-card p:last-child {
            margin-bottom: 0;
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

            .btn-outline-custom span {
                display: none;
            }

            .btn-outline-custom {
                padding: 0.5rem 0.75rem;
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
                    <div class="profile-role">{{ Auth::user()->role ?? 'Konsumen' }}</div>
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

        <div class="menu-grid">
            <a href="{{ route('books.index') }}" class="menu-card blue fade-in">
                <div class="menu-icon"><i class="fa fa-book"></i></div>
                <div class="menu-title">Buku</div>
                <div class="menu-description">Kelola koleksi buku perpustakaan</div>
            </a>

            <a href="{{ route('racks.index') }}" class="menu-card green fade-in">
                <div class="menu-icon"><i class="fa fa-archive"></i></div>
                <div class="menu-title">Rak</div>
                <div class="menu-description">Daftar rak penyimpanan buku</div>
            </a>

            <a href="{{ route('rackslocation.index') }}" class="menu-card orange fade-in">
                <div class="menu-icon"><i class="fa fa-map-marker-alt"></i></div>
                <div class="menu-title">Lokasi Rak</div>
                <div class="menu-description">Lokasi tiap rak buku</div>
            </a>

            <a href="{{ route('publisher.index') }}" class="menu-card purple fade-in">
                <div class="menu-icon"><i class="fa fa-building"></i></div>
                <div class="menu-title">Penerbit</div>
                <div class="menu-description">Manajemen penerbit</div>
            </a>

            <a href="{{ route('categories.index') }}" class="menu-card red fade-in">
                <div class="menu-icon"><i class="fa fa-folder"></i></div>
                <div class="menu-title">Kategori</div>
                <div class="menu-description">Atur kategori buku</div>
            </a>

            <a href="{{ route('subcategories.index') }}" class="menu-card teal fade-in">
                <div class="menu-icon"><i class="fa fa-layer-group"></i></div>
                <div class="menu-title">Subkategori</div>
                <div class="menu-description">Kelola sub kategori</div>
            </a>
        </div>

        <div class="about-card fade-in">
            <h5><i class="fa fa-info-circle me-2"></i> Tentang Kami</h5>
            <p>Bimantara Pustaka adalah sistem perpustakaan digital modern yang memudahkan pengelolaan buku, kategori, subkategori, penerbit, hingga manajemen pengguna.</p>
        </div>

        <div class="text-center mb-5 fade-in">
            <div class="dropdown d-inline-block">
                <button class="btn btn-profile dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fa fa-user me-2"></i>{{ Auth::user()->name ?? 'Profile' }}
                </button>
                <ul class="dropdown-menu profile-dropdown">
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="fa fa-user me-2"></i> Profil Saya
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
