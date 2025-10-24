<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - Perpustakaan Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .animated-gradient {
            background: linear-gradient(135deg, #1a202c, #2d3748, #4a5568);
            background-size: 400% 400%;
            animation: gradientMove 10s ease infinite;
        }

        .particle {
            position: absolute;
            width: 2px;
            height: 2px;
            background: rgba(255, 215, 0, 0.7); /* Emas dengan sedikit transparansi */
            border-radius: 50%;
            animation: float 8s infinite linear;
        }

        @keyframes float {
            0% { transform: translateY(100vh) translateX(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100vh) translateX(150px); opacity: 0; }
        }

        .particle:nth-child(1) { left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { left: 20%; animation-delay: 1s; }
        .particle:nth-child(3) { left: 30%; animation-delay: 2s; }
        .particle:nth-child(4) { left: 40%; animation-delay: 3s; }
        .particle:nth-child(5) { left: 50%; animation-delay: 4s; }
        .particle:nth-child(6) { left: 60%; animation-delay: 5s; }
        .particle:nth-child(7) { left: 70%; animation-delay: 0.5s; }
        .particle:nth-child(8) { left: 80%; animation-delay: 1.5s; }
        .particle:nth-child(9) { left: 90%; animation-delay: 2.5s; }
        .particle:nth-child(10) { left: 15%; animation-delay: 3.5s; }

        .form-card {
            background: rgba(26, 32, 44, 0.95); /* Latar gelap elegan */
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3), 0 0 20px rgba(255, 215, 0, 0.1); /* Efek reflektif */
            backdrop-filter: blur(10px); /* Efek kaca reflektif */
            border: 1px solid rgba(255, 215, 0, 0.2); /* Border emas tipis */
        }

        .profile-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #FFD700; /* Emas untuk aksen */
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3); /* Bayangan emas */
        }

        .image-upload {
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .image-upload input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .input-field {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 215, 0, 0.1);
            color: #e2e8f0;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .input-field:focus {
            border-color: #FFD700;
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.2);
            outline: none;
        }

        .btn {
            background: linear-gradient(45deg, #FFD700, #FFA500);
            color: #1a202c;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.querySelector('input[name="photo"]');
            const img = document.querySelector('.profile-image');

            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-900">
<div class="relative w-full h-screen animated-gradient overflow-hidden">
    <!-- Partikel Cahaya -->
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>

    <!-- Formulir Registrasi -->
    <div class="w-full max-w-md form-card p-8 space-y-6 mx-auto">
        <!-- Profile Photo -->
        <div class="flex justify-center">
            <div class="image-upload relative">
                <img src="{{ asset('images/default-profile.png') }}" alt="Profile Image" class="profile-image">
                <input type="file" name="photo" accept="image/*" class="absolute inset-0">
            </div>
        </div>
        @error('photo')
        <p class="text-sm text-yellow-400 text-center">{{ $message }}</p>
        @enderror

        <h2 class="text-2xl font-bold text-yellow-400 text-center">Create Account</h2>
        <p class="text-gray-300 text-center text-sm">Please enter your details to register</p>

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Full Name -->
            <div>
                <label class="block text-sm font-medium text-gray-300">Full Name</label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       required
                       autofocus
                       class="w-full px-4 py-3 input-field rounded-lg placeholder-gray-400 text-sm"
                       placeholder="Enter your full name">
                @error('name')
                <p class="mt-1 text-sm text-yellow-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-300">Email Address</label>
                <input type="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       class="w-full px-4 py-3 input-field rounded-lg placeholder-gray-400 text-sm"
                       placeholder="Enter your email">
                @error('email')
                <p class="mt-1 text-sm text-yellow-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-medium text-gray-300">Password</label>
                <input type="password"
                       name="password"
                       required
                       class="w-full px-4 py-3 input-field rounded-lg placeholder-gray-400 text-sm"
                       placeholder="Create a password">
                @error('password')
                <p class="mt-1 text-sm text-yellow-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-sm font-medium text-gray-300">Confirm Password</label>
                <input type="password"
                       name="password_confirmation"
                       required
                       class="w-full px-4 py-3 input-field rounded-lg placeholder-gray-400 text-sm"
                       placeholder="Confirm your password">
                @error('password_confirmation')
                <p class="mt-1 text-sm text-yellow-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Terms -->
            <div class="flex items-center text-sm">
                <label class="flex items-center">
                    <input type="checkbox" name="terms" required class="mr-2 rounded text-yellow-400">
                    <span class="text-gray-300">I agree to the Terms and Privacy Policy</span>
                </label>
            </div>

            <!-- Register Button -->
            <button type="submit"
                    class="w-full btn py-3 px-4 rounded-lg font-semibold text-sm">
                Sign Up
            </button>
        </form>
    </div>
</div>
</body>
</html>
