<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bimantara Pustaka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background */
        .animated-bg {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
            overflow: hidden;
        }

        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            animation: float 20s infinite ease-in-out;
        }

        .shape:nth-child(1) {
            width: 300px;
            height: 300px;
            top: -150px;
            left: -150px;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 200px;
            height: 200px;
            top: 50%;
            right: -100px;
            animation-delay: 4s;
        }

        .shape:nth-child(3) {
            width: 250px;
            height: 250px;
            bottom: -125px;
            left: 30%;
            animation-delay: 2s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(180deg); }
        }

        /* Header */
        .top-header {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 20px 0;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .library-title {
            font-size: 28px;
            font-weight: 800;
            margin: 0;
            background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Realtime Clock in Header */
        .header-clock {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
        }

        .header-clock-time {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header-clock-date {
            font-size: 13px;
            color: #6c757d;
            font-weight: 500;
        }

        .profile-section {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 8px 20px;
            background: rgba(74, 85, 104, 0.1);
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .profile-photo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 3px solid #4a5568;
            box-shadow: 0 4px 15px rgba(74, 85, 104, 0.3);
            object-fit: cover;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
            text-align: right;
        }

        .profile-name {
            font-weight: 700;
            font-size: 16px;
            color: #2d3748;
        }

        .profile-role {
            font-size: 13px;
            color: #718096;
            font-style: italic;
        }

        .btn-outline-custom {
            border: 2px solid #4a5568;
            color: #4a5568;
            font-weight: 600;
            padding: 8px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .btn-outline-custom:hover {
            background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
            color: white;
            border-color: transparent;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(74, 85, 104, 0.3);
        }

        /* Content Container */
        .content-wrapper {
            position: relative;
            z-index: 5;
            padding: 40px 0;
        }

        /* Welcome Section */
        .welcome-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 50px;
            margin: 30px 0;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(74, 85, 104, 0.1) 0%, transparent 70%);
            animation: pulse 8s infinite ease-in-out;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .welcome-section h2 {
            position: relative;
            font-size: 36px;
            font-weight: 800;
            background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
        }

        .welcome-section p {
            position: relative;
            font-size: 18px;
            color: #4a5568;
        }

        /* Menu Grid */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }

        .menu-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            display: block;
        }

        .menu-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(74, 85, 104, 0.1) 0%, rgba(45, 55, 72, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .menu-card:hover::before {
            opacity: 1;
        }

        .menu-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.2);
        }

        .menu-icon {
            width: 90px;
            height: 90px;
            margin: 0 auto 25px;
            border-radius: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
            transition: all 0.4s ease;
            position: relative;
        }

        .menu-card:hover .menu-icon {
            transform: scale(1.15) rotate(10deg);
        }

        .menu-card.blue .menu-icon {
            background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
            color: white;
            box-shadow: 0 15px 35px rgba(74, 85, 104, 0.4);
        }

        .menu-card.green .menu-icon {
            background: linear-gradient(135deg, #718096 0%, #4a5568 100%);
            color: white;
            box-shadow: 0 15px 35px rgba(113, 128, 150, 0.4);
        }

        .menu-card.orange .menu-icon {
            background: linear-gradient(135deg, #a0aec0 0%, #718096 100%);
            color: white;
            box-shadow: 0 15px 35px rgba(160, 174, 192, 0.4);
        }

        .menu-card.purple .menu-icon {
            background: linear-gradient(135deg, #cbd5e0 0%, #a0aec0 100%);
            color: white;
            box-shadow: 0 15px 35px rgba(203, 213, 224, 0.4);
        }

        .menu-title {
            font-size: 22px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 12px;
            position: relative;
        }

        .menu-description {
            font-size: 15px;
            color: #718096;
            position: relative;
        }

        /* About Section */
        .about-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            margin: 40px 0;
        }

        .about-card h5 {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
        }

        .about-card p {
            font-size: 16px;
            line-height: 1.8;
            color: #4a5568;
        }

        /* Profile Dropdown */
        .profile-dropdown {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            border: none;
            padding: 10px;
        }

        .profile-dropdown .dropdown-item {
            border-radius: 12px;
            padding: 12px 20px;
            margin: 5px 0;
            transition: all 0.3s ease;
        }

        .profile-dropdown .dropdown-item:hover {
            background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
            color: white;
            transform: translateX(5px);
        }

        .btn-profile {
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid #4a5568;
            color: #4a5568;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 30px;
            font-size: 18px;
            transition: all 0.3s ease;
        }

        .btn-profile:hover {
            background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
            color: white;
            border-color: transparent;
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(74, 85, 104, 0.4);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .library-title {
                font-size: 20px;
            }

            .header-clock {
                display: none;
            }

            .welcome-section {
                padding: 30px 20px;
            }

            .welcome-section h2 {
                font-size: 28px;
            }

            .welcome-section p {
                font-size: 16px;
            }

            .menu-grid {
                grid-template-columns: 1fr;
            }

            .profile-section {
                gap: 10px;
                padding: 5px 15px;
            }

            .btn-outline-custom {
                padding: 6px 12px;
                font-size: 14px;
            }

            .btn-outline-custom .fa {
                display: none;
            }
        }

        /* Loading Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease forwards;
        }

        .delay-1 { animation-delay: 0.1s; opacity: 0; }
        .delay-2 { animation-delay: 0.2s; opacity: 0; }
        .delay-3 { animation-delay: 0.3s; opacity: 0; }
        .delay-4 { animation-delay: 0.4s; opacity: 0; }
    </style>
</head>
<body>
<!-- Animated Background -->
<div class="animated-bg">
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
</div>

{{-- Header --}}
<div class="top-header">
    <div class="container">
        <div class="header-content">
            <h1 class="library-title">
                <i class="fas fa-book-open me-2"></i>
                Bimantara Pustaka
            </h1>

            {{-- Realtime Clock --}}
            <div class="header-clock">
                <div class="header-clock-time" id="header-time">00:00:00</div>
                <div class="header-clock-date" id="header-date">Loading...</div>
            </div>

            <div class="profile-section">
                <a href="{{ route('users.index') }}" class="btn btn-outline-custom">
                    <i class="fa fa-users-cog me-1"></i> Kelola User
                </a>
                @if(Auth::user()->photo)
                    <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="Profile" class="profile-photo">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4a5568&color=fff&size=50" alt="Profile" class="profile-photo">
                @endif
                <div class="profile-info">
                    <div class="profile-name">
                        {{ Auth::user()->name ?? 'Administrator' }}
                    </div>
                    <div class="profile-role">
                        {{ Auth::user()->role ?? 'Admin Perpustakaan' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Content --}}
<div class="content-wrapper">
    <div class="container">
        <!-- Welcome Section -->
        <div class="welcome-section fade-in-up delay-1">
            <h2>Selamat Datang, {{ Auth::user()->name ?? 'User' }}! 👋</h2>
            <p>Kelola perpustakaan digital dengan mudah, cepat, dan terorganisir</p>
        </div>

        <!-- Menu Cards -->
        <div class="menu-grid">
            <a href="{{ route('books.index') }}" class="menu-card blue fade-in-up delay-2">
                <div class="menu-icon">
                    <i class="fa fa-book"></i>
                </div>
                <div class="menu-title">Buku</div>
                <div class="menu-description">Kelola koleksi buku perpustakaan</div>
            </a>

            <a href="{{ route('categories.index') }}" class="menu-card green fade-in-up delay-2">
                <div class="menu-icon">
                    <i class="fa fa-folder"></i>
                </div>
                <div class="menu-title">Kategori</div>
                <div class="menu-description">Atur kategori buku</div>
            </a>

            <a href="{{ route('subcategories.index') }}" class="menu-card orange fade-in-up delay-2">
                <div class="menu-icon">
                    <i class="fa fa-layer-group"></i>
                </div>
                <div class="menu-title">Subkategori</div>
                <div class="menu-description">Kelola sub kategori</div>
            </a>

            <a href="{{ route('publisher.index') }}" class="menu-card purple fade-in-up delay-3">
                <div class="menu-icon">
                    <i class="fa fa-building"></i>
                </div>
                <div class="menu-title">Penerbit</div>
                <div class="menu-description">Manajemen penerbit</div>
            </a>
        </div>

        <!-- About Section -->
        <div class="about-card fade-in-up delay-3">
            <h5><i class="fa fa-info-circle me-2"></i> Tentang Kami</h5>
            <p>
                Bimantara Pustaka adalah sistem perpustakaan digital modern yang memudahkan pengelolaan buku,
                kategori, subkategori, penerbit, hingga manajemen pengguna.
            </p>
            <p class="mb-0">
                Dengan teknologi terkini, kami berkomitmen untuk menciptakan akses literasi yang mudah, cepat,
                dan terorganisir bagi semua kalangan. Visi kami adalah menjadi mitra literasi digital yang mendukung
                perkembangan pendidikan, riset, dan budaya membaca di era modern.
            </p>
        </div>

        <!-- Profile Dropdown -->
        <div class="text-center mb-5 fade-in-up delay-4">
            @auth
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
            @endauth
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Realtime Clock
    function updateClock() {
        const now = new Date();

        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const time = `${hours}:${minutes}:${seconds}`;

        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        const dayName = days[now.getDay()];
        const day = now.getDate();
        const month = months[now.getMonth()];
        const year = now.getFullYear();

        const date = `${dayName}, ${day} ${month} ${year}`;

        document.getElementById('header-time').textContent = time;
        document.getElementById('header-date').textContent = date;
    }

    updateClock();
    setInterval(updateClock, 1000);

    // Add ripple effect on card click
    document.querySelectorAll('.menu-card').forEach(card => {
        card.addEventListener('click', function(e) {
            const ripple = document.createElement('div');
            ripple.style.position = 'absolute';
            ripple.style.width = ripple.style.height = '100px';
            ripple.style.background = 'rgba(255, 255, 255, 0.5)';
            ripple.style.borderRadius = '50%';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s ease-out';
            ripple.style.pointerEvents = 'none';

            const rect = this.getBoundingClientRect();
            ripple.style.left = (e.clientX - rect.left - 50) + 'px';
            ripple.style.top = (e.clientY - rect.top - 50) + 'px';

            this.appendChild(ripple);

            setTimeout(() => ripple.remove(), 600);
        });
    });

    // Add CSS for ripple animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
</script>
</body>
</html>
