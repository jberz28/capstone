<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'From Campus to Career - USTP Balubal Graduate Tracking System')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-[#003057] to-[#f8a105]">
    <div id="app">
        <!-- Navigation removed to show full background -->

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-12">
            <div class="max-w-7xl mx-auto px-4">
                <div class="grid md:grid-cols-4 gap-8">
                    <div>
                        <div class="flex items-center space-x-2 mb-4">
                            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-white"></i>
                            </div>
                            <h3 class="text-xl font-bold">USTP Balubal</h3>
                        </div>
                        <p class="text-gray-300">
                            Graduate Tracking System for monitoring professional pathways and career development.
                        </p>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                        <ul class="space-y-2 text-gray-300">
                            <li><a href="{{ route('dashboard') }}" class="hover:text-white transition-colors">Home</a></li>
                            @auth
                                @if(auth()->user()->isGraduate())
                                    <li><a href="{{ route('graduate.dashboard') }}" class="hover:text-white transition-colors">Dashboard</a></li>
                                    <li><a href="{{ route('graduate.profile') }}" class="hover:text-white transition-colors">Profile</a></li>
                                @elseif(auth()->user()->isStaff())
                                    <li><a href="{{ route('staff.dashboard') }}" class="hover:text-white transition-colors">Dashboard</a></li>
                                    <li><a href="{{ route('staff.graduates') }}" class="hover:text-white transition-colors">Graduates</a></li>
                                @elseif(auth()->user()->isAdmin())
                                    <li><a href="{{ route('admin.dashboard') }}" class="hover:text-white transition-colors">Dashboard</a></li>
                                    <li><a href="{{ route('admin.users') }}" class="hover:text-white transition-colors">Users</a></li>
                                @endif
                            @else
                                <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Login</a></li>
                                <li><a href="mailto:admin@ustp.edu.ph" class="hover:text-white transition-colors">Contact Admin</a></li>
                            @endauth
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Contact</h4>
                        <div class="space-y-2 text-gray-300">
                            <p><i class="fas fa-map-marker-alt mr-2"></i> USTP Balubal Campus</p>
                            <p><i class="fas fa-phone mr-2"></i> +63 XXX XXX XXXX</p>
                            <p><i class="fas fa-envelope mr-2"></i> info@ustp.edu.ph</p>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Alumni Contact</h4>
                        <div class="space-y-2 text-gray-300">
                            <p class="font-bold text-yellow-400">Ms. Jamie Cruz, Coordinator</p>
                            <p><i class="fas fa-phone mr-2"></i> +63 905 123 4567</p>
                            <p><i class="fas fa-envelope mr-2"></i> alumni.balubal@ustp.edu.ph</p>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
                    <p>&copy; {{ date('Y') }} USTP Balubal Graduate Tracking System. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    <script>
        function toggleUserMenu() {
            const userMenu = document.getElementById('user-menu');
            userMenu.classList.toggle('hidden');
        }

        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('user-menu');
            const userButton = event.target.closest('[onclick="toggleUserMenu()"]');
            
            if (!userButton && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>