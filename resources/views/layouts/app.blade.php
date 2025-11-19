<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bimantara Pustaka - @yield('title')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --body-bg: #f4f6f9;
            --body-color: #333;
            --sidebar-bg: #fff;
            --sidebar-color: #333;
            --navbar-bg: linear-gradient(90deg, #007bff, #0056d8);
            --card-bg: #fff;
            --card-border: rgba(0,0,0,0.1);
            --table-bg: #fff;
            --table-border: #dee2e6;
            --table-header-bg: #f8f9fa;
            --table-header-color: #333;
        }

        [data-bs-theme="dark"] {
            --body-bg: #1a1a1a;
            --body-color: #e5e5e5;
            --sidebar-bg: #2c2c2c;
            --sidebar-color: #e5e5e5;
            --navbar-bg: linear-gradient(90deg, #004d99, #003366);
            --card-bg: #2c2c2c;
            --card-border: #444;
            --table-bg: #2c2c2c;
            --table-border: #444;
            --table-header-bg: #343a40;
            --table-header-color: #e5e5e5;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--body-bg);
            color: var(--body-color);
            margin: 0;
            padding: 0;
            transition: background-color 0.4s, color 0.4s;
        }

        .navbar {
            background: var(--navbar-bg);
            box-shadow: 0 3px 6px rgba(0,0,0,0.15);
            height: 70px;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: #fff;
        }

        .sidebar {
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--card-border);
            width: 270px;
            padding-top: 1.5rem;
            box-shadow: 2px 0 8px rgba(0,0,0,0.05);
            position: fixed;
            top: 70px;
            bottom: 0;
            left: 0;
            overflow-y: auto;
            transition: background-color 0.4s, color 0.4s;
        }

        .sidebar .nav-link {
            font-size: 1rem;
            padding: 12px 25px;
            color: var(--sidebar-color);
            border-radius: 10px;
            margin: 3px 10px;
            transition: background 0.3s, color 0.3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #007bff;
            color: #fff !important;
            font-weight: 600;
        }

        .sidebar .dropdown-menu {
            background-color: var(--sidebar-bg);
            border: none;
            box-shadow: none;
        }

        .sidebar .dropdown-item {
            color: var(--sidebar-color);
            padding: 8px 25px;
            margin: 0 10px;
            border-radius: 5px;
        }

        .sidebar .dropdown-item:hover,
        .sidebar .dropdown-item.active {
            background-color: #007bff;
            color: #fff !important;
        }

        .content-wrapper {
            margin-left: 270px;
            margin-top: 70px;
            padding: 30px;
            padding-bottom: 70px;
            background-color: var(--body-bg);
            color: var(--body-color);
            transition: background-color 0.4s, color 0.4s;
            min-height: calc(100vh - 70px);
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 270px;
            right: 0;
            background-color: var(--body-bg);
            border-top: 1px solid var(--card-border);
            padding: 10px;
            text-align: center;
            color: var(--body-color);
            z-index: 1030;
            transition: background-color 0.4s, color 0.4s;
        }

        .toggle-dark {
            background: transparent;
            border: none;
            color: #fff;
            font-size: 1.3rem;
            cursor: pointer;
        }

        /* Notifications Styles */
        .notification-bell {
            position: relative;
            background: transparent;
            border: none;
            color: #fff;
            font-size: 1.3rem;
            cursor: pointer;
            padding: 0;
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 11px;
            font-weight: bold;
            min-width: 18px;
            text-align: center;
        }

        .notification-dropdown {
            width: 360px;
            max-height: 450px;
            overflow-y: auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid var(--card-border);
            transition: background-color 0.2s;
            cursor: pointer;
            text-decoration: none;
            display: block;
        }

        .notification-item:hover {
            background-color: var(--table-header-bg);
        }

        .notification-item.unread {
            background-color: rgba(0, 123, 255, 0.05);
            border-left: 3px solid #007bff;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #007bff, #0056d8);
            color: white;
            font-size: 18px;
        }

        .notification-time {
            font-size: 11px;
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                top: 0;
                height: auto;
                position: relative;
            }
            .content-wrapper {
                margin-left: 0;
                margin-top: 70px;
            }
            footer {
                left: 0;
            }
            .notification-dropdown {
                width: 300px;
            }
        }

        @stack('styles')
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark px-4">
    <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
        <i class="fas fa-book-open me-2"></i> Bimantara Pustaka
    </a>

    <div class="ms-auto d-flex align-items-center gap-3">
        <!-- Dark Mode Toggle -->
        <button class="toggle-dark" id="toggleDarkMode" title="Toggle Dark Mode">
            <i class="fa-solid fa-moon"></i>
        </button>



        <!-- User Dropdown -->
        <div class="dropdown">
            <a class="text-white text-decoration-none dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name ?? 'Guest' }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user me-2"></i> Profil</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

{{-- Sidebar utama (ganti path sesuai projectmu jika beda) --}}
<nav class="sidebar">
    <ul class="nav flex-column">
        {{-- DASHBOARD --}}
        <li class="nav-item text-uppercase fw-bold px-3 mt-2 small text-secondary">Dashboard</li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="fa fa-home"></i> Dashboard
            </a>
        </li>

        {{-- LAYANAN SISTEM --}}
        <li class="nav-item text-uppercase fw-bold px-3 mt-4 small text-secondary">Layanan Sistem</li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('books.index') }}">
                <i class="fa fa-book"></i> Buku
            </a>
        </li>

        @if(auth()->check() && strtolower(auth()->user()->role) === 'admin')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('members.index') }}">
                    <i class="fa fa-users"></i> Members
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('racks.index') }}">
                    <i class="fa fa-archive"></i> Rak
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('rackslocation.index') }}">
                    <i class="fa fa-map-marker-alt"></i> Lokasi Rak
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('categories.index') }}">
                    <i class="fa fa-folder"></i> Kategori
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('subcategories.index') }}">
                    <i class="fa fa-layer-group"></i> Subkategori
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('sortbooks.index') }}">
                    <i class="fa fa-sort"></i> Penataan
                </a>
            </li>
        @endif

        <li class="nav-item">
            <a class="nav-link" href="{{ route('publisher.index') }}">
                <i class="fa fa-building"></i> Penerbit
            </a>
        </li>

        {{-- TRANSAKSI --}}
        <li class="nav-item text-uppercase fw-bold px-3 mt-4 small text-secondary">Transaksi</li>
        @if(auth()->check() && in_array(strtolower(auth()->user()->role), ['admin', 'konsumen']))
            <li class="nav-item">
                <a class="nav-link" href="{{ route('borrowing.index') }}">
                    <i class="fa fa-exchange-alt"></i> Peminjaman
                </a>
            </li>
        @endif

        {{-- ADMINISTRASI SISTEM --}}
        @if(auth()->check() && strtolower(auth()->user()->role) === 'admin')
            <li class="nav-item text-uppercase fw-bold px-3 mt-4 small text-secondary">Administrasi Sistem</li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('users.index') }}">
                    <i class="fa fa-user-cog"></i> Manajemen User
                </a>
            </li>
        @endif
    </ul>
</nav>

<!-- Content -->
<div class="content-wrapper">
    <div class="container-fluid">
        @yield('content')
    </div>
</div>

<!-- Footer -->
<footer>
    <strong>© 2025 Bimantara Pustaka</strong> — Semua Hak Dilindungi.
</footer>

<!-- Script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Dark Mode Toggle
    const toggleBtn = document.getElementById('toggleDarkMode');
    const body = document.body;
    const icon = toggleBtn.querySelector('i');

    const savedTheme = localStorage.getItem('theme') || 'light';
    if (savedTheme === 'dark') {
        body.setAttribute('data-bs-theme', 'dark');
        icon.classList.replace('fa-moon', 'fa-sun');
    }

    toggleBtn.addEventListener('click', () => {
        const currentTheme = body.getAttribute('data-bs-theme') || 'light';
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';

        body.setAttribute('data-bs-theme', newTheme);
        localStorage.setItem('theme', newTheme);

        if (newTheme === 'dark') {
            icon.classList.replace('fa-moon', 'fa-sun');
        } else {
            icon.classList.replace('fa-sun', 'fa-moon');
        }
    });

    // Mark single notification as read
    function markAsRead(notificationId, event) {
        event.preventDefault();

        fetch(`/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update badge count
                    const badge = document.querySelector('.notification-badge');
                    const count = parseInt(badge?.textContent || 0) - 1;

                    if (count > 0) {
                        badge.textContent = count;
                    } else {
                        badge?.remove();
                    }

                    // Navigate to the notification URL
                    const link = event.currentTarget;
                    if (link.href && link.href !== '#') {
                        window.location.href = link.href;
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Mark all notifications as read
    function markAllAsRead() {
        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove badge
                    document.querySelector('.notification-badge')?.remove();

                    // Remove unread styling
                    document.querySelectorAll('.notification-item.unread').forEach(item => {
                        item.classList.remove('unread');
                    });

                    // Hide "mark all as read" link
                    const markAllLink = document.querySelector('a[onclick="markAllAsRead()"]');
                    if (markAllLink) {
                        markAllLink.parentElement.remove();
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    }
</script>
@stack('scripts')
</body>
</html>
