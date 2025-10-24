<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Perpustakaan Digital</title>
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
            background: rgba(255, 215, 0, 0.7); /* Emas dengan transparansi */
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
        .particle:nth-child(11) { left: 25%; animation-delay: 4.5s; }
        .particle:nth-child(12) { left: 35%; animation-delay: 0.8s; }
        .particle:nth-child(13) { left: 45%; animation-delay: 1.8s; }
        .particle:nth-child(14) { left: 55%; animation-delay: 2.8s; }
        .particle:nth-child(15) { left: 65%; animation-delay: 3.8s; }
        .particle:nth-child(16) { left: 75%; animation-delay: 4.8s; }
        .particle:nth-child(17) { left: 85%; animation-delay: 1.2s; }
        .particle:nth-child(18) { left: 95%; animation-delay: 2.2s; }
        .particle:nth-child(19) { left: 5%; animation-delay: 3.2s; }
        .particle:nth-child(20) { left: 95%; animation-delay: 4.2s; }

        .form-card {
            background: rgba(26, 32, 44, 0.95); /* Latar gelap elegan */
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3), 0 0 20px rgba(255, 215, 0, 0.1); /* Efek reflektif */
            backdrop-filter: blur(10px); /* Efek kaca reflektif */
            border: 1px solid rgba(255, 215, 0, 0.2); /* Border emas tipis */
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
</head>
<body class="min-h-screen bg-gray-900">
<div class="flex h-screen w-full">
    <!-- LEFT SIDE - Form Login -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
        <div class="w-full max-w-md form-card p-8 space-y-6">
            <!-- Header -->
            <div class="mb-8 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-12 h-12 bg-gray-700 rounded-full flex items-center justify-center">
                        @if(file_exists(public_path('images/logo.png.png')))
                            <span class="text-white font-bold"><img src="logo.png.png" alt=""></span>
                        @else
                            <span class="text-white font-bold"><img src="logo.png.png" alt=""></span>
                        @endif
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-yellow-400 mb-2">Welcome Back</h2>
                <p class="text-gray-300 text-sm">Please enter your details</p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Email Address</label>
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           class="w-full px-4 py-3 input-field rounded-lg placeholder-gray-400 text-sm"
                           placeholder="Enter your email">
                    @error('email')
                    <p class="mt-1 text-sm text-yellow-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                    <input type="password"
                           name="password"
                           required
                           class="w-full px-4 py-3 input-field rounded-lg placeholder-gray-400 text-sm"
                           placeholder="Enter your password">
                    @error('password')
                    <p class="mt-1 text-sm text-yellow-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="mr-2 rounded text-yellow-400">
                        <span class="text-gray-300">Remember for 30 days</span>
                    </label>
                    <a href="#" class="text-yellow-400 hover:text-yellow-300">Forgot password?</a>
                </div>

                <button type="submit"
                        class="w-full btn py-3 px-4 rounded-lg font-semibold text-sm">
                    Sign In
                </button>

                <!-- Google Sign In Button -->
            </form>

            <p class="mt-6 text-center text-sm text-gray-300">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-yellow-400 hover:text-yellow-300 font-medium">Sign up</a>
            </p>
        </div>
    </div>

    <!-- RIGHT SIDE - Background -->
    <div class="hidden lg:block lg:w-1/2 animated-gradient relative overflow-hidden">
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

        <div class="h-full flex items-center justify-center text-white text-center p-8 relative z-10">
            <div>
                @if(file_exists(public_path('images/logo.png.png')))
                    <span class="text-white font-bold"><img src="logo.png.png" alt=""></span>
                @else
                    <span class="text-white font-bold"><img src="logo.png.png" alt=""></span>
                @endif
            </div>
        </div>
    </div>
</div>
</body>
</html>
