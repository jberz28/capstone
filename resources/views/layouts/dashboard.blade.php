<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - Alumni USTP Balubal Portal</title>

    <!-- Enhanced Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        :root {
            --font-primary: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-heading: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --color-primary: #2563eb;
            --color-primary-dark: #1d4ed8;
            --color-secondary: #64748b;
            --color-success: #10b981;
            --color-warning: #f59e0b;
            --color-danger: #ef4444;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }
        
        body {
            font-family: var(--font-primary);
            font-weight: 400;
            line-height: 1.6;
            color: #1f2937;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            font-weight: 600;
            line-height: 1.3;
            color: #111827;
        }
        
        .font-display {
            font-family: var(--font-heading);
            font-weight: 700;
        }
        
        .font-body {
            font-family: var(--font-primary);
        }
        
        .text-gradient {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .card-enhanced {
            background: white;
            border-radius: 16px;
            box-shadow: var(--shadow-md);
            border: 1px solid #f1f5f9;
            transition: all 0.3s ease;
        }
        
        .card-enhanced:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            font-size: 14px;
            color: white;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }
        
        .input-enhanced {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: white;
        }
        
        .input-enhanced:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgb(37 99 235 / 0.1);
            outline: none;
        }
        
        .input-enhanced:not(:disabled) {
            pointer-events: auto !important;
            cursor: text !important;
        }
        
        .table-enhanced {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            border: 1px solid #f1f5f9;
        }
        
        .table-enhanced th {
            background: #f8fafc;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            padding: 16px 24px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .table-enhanced td {
            padding: 16px 24px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }
        
        .table-enhanced tbody tr:hover {
            background: #f8fafc;
        }
        
        .badge-enhanced {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .sidebar-enhanced {
            background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 100%);
            box-shadow: var(--shadow-xl);
        }
        
        .nav-item-enhanced {
            border-radius: 12px;
            padding: 12px 16px;
            margin: 4px 0;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .nav-item-enhanced:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(4px);
        }
        
        .nav-item-enhanced.active {
            background: rgba(255, 255, 255, 0.2);
            box-shadow: var(--shadow-md);
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-72 lg:w-64 -translate-x-full lg:translate-x-0 sidebar-enhanced text-white flex flex-col fixed lg:relative inset-y-0 left-0 z-50 transform transition-all duration-300 ease-in-out" id="sidebar">
            <!-- Logo and Toggle -->
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <!-- University Style Logo Card -->
                    <div class="bg-black-500 rounded-lg p-3 flex items-center justify-between w-full" id="logo-content">
                        <div class="w-16 h-16 bg-white rounded-lg flex items-center justify-center shadow-sm overflow-hidden mx-auto">
                            <img src="{{ asset('images/alumni.png') }}" 
                                 alt="USTP Logo" 
                                 class="w-full h-full object-contain"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-full h-full bg-blue-500 rounded flex items-center justify-center" style="display: none;">
                                <i class="fas fa-university text-white text-lg"></i>
                            </div>
                        </div>
                        <!-- Fold Toggle Button (desktop) -->
                        <button onclick="toggleSidebarFold()" class="hidden lg:block p-1 text-white hover:text-blue-200 transition-colors" id="fold-toggle">
                            <i class="fas fa-chevron-left text-xs"></i>
                        </button>
                        <!-- Close Button (mobile) -->
                        <button onclick="toggleSidebar()" class="lg:hidden p-2 text-white/80 hover:text-white transition-colors" aria-label="Close menu">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-4 space-y-1">
                @if(auth()->user()->isGraduate())
                    <!-- Graduate Navigation -->
                    <a href="{{ route('graduate.dashboard') }}" class="w-full flex items-center space-x-4 nav-item-enhanced active">
                        <i class="fas fa-tachometer-alt text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Dashboard</span>
                    </a>
                <a href="{{ route('graduate.profile.enhanced') }}" class="w-full flex items-center space-x-4 nav-item-enhanced">
                    <i class="fas fa-user-edit text-white text-lg"></i>
                    <span class="text-white font-medium nav-text">Complete Profile</span>
                </a>
                    <a href="{{ route('graduate.resume') }}" class="w-full flex items-center space-x-4 nav-item-enhanced">
                        <i class="fas fa-file-alt text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Resume</span>
                    </a>
                    <a href="{{ route('graduate.jobs') }}" class="w-full flex items-center space-x-4 nav-item-enhanced">
                        <i class="fas fa-search text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Job Search</span>
                    </a>
                    <a href="{{ route('graduate.alumni-activities') }}" class="w-full flex items-center space-x-4 nav-item-enhanced">
                        <i class="fas fa-calendar-alt text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Alumni Activities</span>
                    </a>
                    <a href="{{ route('graduate.alumni-membership') }}" class="w-full flex items-center space-x-4 nav-item-enhanced">
                        <i class="fas fa-id-card text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Alumni Membership</span>
                    </a>
                    <a href="{{ route('graduate.announcements') }}" class="w-full flex items-center space-x-4 nav-item-enhanced">
                        <i class="fas fa-bullhorn text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Announcements</span>
                    </a>
                    <a href="{{ route('graduate.survey.index') }}" class="w-full flex items-center space-x-4 nav-item-enhanced">
                        <i class="fas fa-clipboard-list text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Alumni Survey</span>
                    </a>
                    <a href="{{ route('graduate.graduation-application') }}" class="w-full flex items-center space-x-4 nav-item-enhanced">
                        <i class="fas fa-graduation-cap text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Application for Graduation</span>
                    </a>

                @elseif(auth()->user()->isStaff())
                    <!-- Staff Navigation -->
                    <a href="{{ route('staff.dashboard') }}" class="w-full flex items-center space-x-4 nav-item-enhanced active">
                        <i class="fas fa-tachometer-alt text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Dashboard</span>
                    </a>
                    <a href="{{ route('staff.graduates') }}" class="w-full flex items-center space-x-4 nav-item-enhanced">
                        <i class="fas fa-users text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Graduates</span>
                    </a>
                    <a href="{{ route('staff.career-support') }}" class="w-full flex items-center space-x-4 nav-item-enhanced">
                        <i class="fas fa-hands-helping text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Career Support</span>
                    </a>
                    <a href="{{ route('staff.job-postings') }}" class="w-full flex items-center space-x-4 nav-item-enhanced">
                        <i class="fas fa-briefcase text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Job Postings</span>
                    </a>
                    <a href="{{ route('staff.alumni') }}" class="w-full flex items-center space-x-4 nav-item-enhanced">
                        <i class="fas fa-graduation-cap text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Alumni</span>
                    </a>
                    <a href="{{ route('staff.surveys.index') }}" class="w-full flex items-center space-x-4 nav-item-enhanced">
                        <i class="fas fa-clipboard-list text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Alumni Surveys</span>
                    </a>
                    <a href="{{ route('staff.announcements.index') }}" class="w-full flex items-center space-x-4 nav-item-enhanced">
                        <i class="fas fa-bullhorn text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Announcements</span>
                    </a>
                    <a href="{{ route('staff.graduation-applications') }}" class="w-full flex items-center space-x-4 nav-item-enhanced">
                        <i class="fas fa-graduation-cap text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Graduation Applications</span>
                    </a>

                @elseif(auth()->user()->isAdmin())
                    <!-- Admin Navigation -->
                    <a href="{{ route('admin.dashboard') }}" class="w-full flex items-center space-x-4 nav-item-enhanced active">
                        <i class="fas fa-tachometer-alt text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Dashboard</span>
                    </a>
                    <a href="{{ route('admin.users') }}" class="w-full flex items-center space-x-4 nav-item-enhanced">
                        <i class="fas fa-users text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">User Management</span>
                    </a>
                    <a href="{{ route('admin.job-postings') }}" class="w-full flex items-center space-x-4 nav-item-enhanced">
                        <i class="fas fa-briefcase text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Job Postings</span>
                    </a>
                    <a href="{{ route('admin.job-review') }}" class="w-full flex items-center space-x-4 nav-item-enhanced">
                        <i class="fas fa-clipboard-check text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Job Review</span>
                    </a>
                    <a href="{{ route('admin.alumni-activities') }}" class="w-full flex items-center space-x-4 nav-item-enhanced">
                        <i class="fas fa-calendar-alt text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Alumni Activities</span>
                    </a>
                    <a href="{{ route('admin.alumni-memberships') }}" class="w-full flex items-center space-x-4 nav-item-enhanced">
                        <i class="fas fa-id-card text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Alumni Memberships</span>
                    </a>
                    <a href="{{ route('admin.maintenance') }}" class="w-full flex items-center space-x-4 nav-item-enhanced">
                        <i class="fas fa-cog text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Maintenance</span>
                    </a>
                    <a href="{{ route('admin.surveys.index') }}" class="w-full flex items-center space-x-4 nav-item-enhanced">
                        <i class="fas fa-clipboard-list text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Alumni Surveys</span>
                    </a>
                    <a href="{{ route('admin.announcements.index') }}" class="w-full flex items-center space-x-4 nav-item-enhanced">
                        <i class="fas fa-bullhorn text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Announcements</span>
                    </a>
                    <a href="{{ route('admin.graduation-applications') }}" class="w-full flex items-center space-x-4 nav-item-enhanced">
                        <i class="fas fa-graduation-cap text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Graduation Applications</span>
                    </a>
                @endif

                <!-- Common Navigation -->
                <div class="border-t border-blue-700 pt-4 mt-4">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-4 px-4 py-3 rounded-lg hover:bg-blue-800 transition-colors">
                        <i class="fas fa-home text-white text-lg"></i>
                        <span class="text-white font-medium nav-text">Home</span>
                    </a>
                </div>
            </nav>

        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-0 bg-gradient-to-br from-gray-50 to-gray-100">
            <!-- Top Bar -->
            <header class="bg-white/80 backdrop-blur-sm shadow-sm border-b border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <!-- Mobile Menu Button -->
                            <button class="lg:hidden text-gray-600 hover:text-gray-800" onclick="toggleSidebar()">
                                <i class="fas fa-bars text-lg"></i>
                            </button>
                        </div>
                        
                        <div class="flex items-center space-x-4">

                            <!-- User Profile -->
                            <div class="relative">
                                <button onclick="toggleUserMenu()" class="flex items-center space-x-3 text-gray-900 hover:text-blue-600 transition-colors">
                                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center overflow-hidden" id="header-profile-picture">
                                        @if(auth()->user()->graduate && auth()->user()->graduate->profile_picture)
                                            <img src="{{ asset('storage/' . auth()->user()->graduate->profile_picture) }}" alt="Profile Picture" class="w-full h-full object-cover">
                                        @else
                                            <i class="fas fa-user text-white text-sm"></i>
                                        @endif
                                    </div>
                                    <div class="flex flex-col items-start">
                                        <span class="text-gray-900 font-bold text-sm uppercase">{{ auth()->user()->name }}</span>
                                        <span class="text-blue-600 text-xs uppercase">{{ ucfirst(auth()->user()->role) }}</span>
                                    </div>
                                    <i class="fas fa-chevron-down text-xs text-blue-600"></i>
                                </button>
                                
                                <!-- Dropdown Menu -->
                                <div id="user-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-50">
                                    <div class="py-1">
                                        @if(auth()->user()->isGraduate())
                                            <a href="{{ route('graduate.profile') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                <i class="fas fa-user mr-3 text-gray-400"></i>
                                                My Profile
                                            </a>
                                        @elseif(auth()->user()->isStaff())
                                            <a href="{{ route('staff.graduates') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                <i class="fas fa-user mr-3 text-gray-400"></i>
                                                My Profile
                                            </a>
                                        @elseif(auth()->user()->isAdmin())
                                            <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                <i class="fas fa-user mr-3 text-gray-400"></i>
                                                My Profile
                                            </a>
                                        @endif
                                        <form method="POST" action="{{ route('logout') }}" class="block">
                                            @csrf
                                            <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                <i class="fas fa-sign-out-alt mr-3 text-gray-400"></i>
                                                Log Out
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto">
                <div class="p-6 sm:p-8">
                    <div id="content-area" class="max-w-7xl mx-auto">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-[1px] z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const body = document.body;

            const isClosed = sidebar.classList.contains('-translate-x-full');
            if (isClosed) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                body.classList.add('overflow-hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                body.classList.remove('overflow-hidden');
            }
        }

        function toggleSidebarFold() {
            const sidebar = document.getElementById('sidebar');
            const logoText = document.getElementById('logo-text');
            const navTexts = document.querySelectorAll('.nav-text');
            const foldToggle = document.getElementById('fold-toggle');
            const mainContent = document.querySelector('.flex-1');
            
            // Toggle sidebar width
            if (sidebar.classList.contains('w-16')) {
                // Expand sidebar
                sidebar.classList.remove('w-16');
                sidebar.classList.add('w-64');
                if (logoText) logoText.classList.remove('hidden');
                navTexts.forEach(text => text.classList.remove('hidden'));
                foldToggle.innerHTML = '<i class="fas fa-chevron-left text-xs"></i>';
                mainContent.classList.remove('lg:ml-0');
                // Save state to localStorage
                localStorage.setItem('sidebarFolded', 'false');
            } else {
                // Collapse sidebar
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-16');
                if (logoText) logoText.classList.add('hidden');
                navTexts.forEach(text => text.classList.add('hidden'));
                foldToggle.innerHTML = '<i class="fas fa-chevron-right text-xs"></i>';
                mainContent.classList.add('lg:ml-0');
                // Save state to localStorage
                localStorage.setItem('sidebarFolded', 'true');
            }
        }

        function restoreSidebarState() {
            const sidebar = document.getElementById('sidebar');
            const logoText = document.getElementById('logo-text');
            const navTexts = document.querySelectorAll('.nav-text');
            const foldToggle = document.getElementById('fold-toggle');
            const mainContent = document.querySelector('.flex-1');
            
            // Check if sidebar was folded
            const isFolded = localStorage.getItem('sidebarFolded') === 'true';
            
            if (isFolded) {
                // Collapse sidebar
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-16');
                if (logoText) logoText.classList.add('hidden');
                navTexts.forEach(text => text.classList.add('hidden'));
                foldToggle.innerHTML = '<i class="fas fa-chevron-right text-xs"></i>';
                mainContent.classList.add('lg:ml-0');
            }
        }

        // Initialize sidebar state on page load
        document.addEventListener('DOMContentLoaded', function() {
            restoreSidebarState();
        });

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

        // Real-time notifications for admins
        @if(auth()->user()->isAdmin())
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize notification dropdown toggle
            const notificationButton = document.getElementById('notification-button');
            const notificationDropdown = document.getElementById('notification-dropdown');
            
            if (notificationButton && notificationDropdown) {
                notificationButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isHidden = notificationDropdown.classList.contains('hidden');
                    notificationDropdown.classList.toggle('hidden');
                    
                    // Load notifications when opening dropdown
                    if (isHidden) {
                        loadNotifications();
                    }
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!notificationButton.contains(e.target) && !notificationDropdown.contains(e.target)) {
                        notificationDropdown.classList.add('hidden');
                    }
                });
            }
            
            // Initialize Echo for real-time notifications
            if (typeof Echo !== 'undefined') {
                Echo.channel('admin-notifications')
                    .listen('new-job-posting', (e) => {
                        console.log('New job posting notification:', e);
                        showNotification(e);
                    });
            } else {
                // Fallback: Poll for new notifications every 30 seconds
                setInterval(checkForNewNotifications, 30000);
            }
        });

        function showNotification(data) {
            // Update notification badge
            const badge = document.getElementById('notification-badge');
            const currentCount = parseInt(badge.textContent) || 0;
            badge.textContent = currentCount + 1;
            badge.classList.remove('hidden');

            // Add notification to list
            const notificationsList = document.getElementById('notifications-list');
            const emptyState = notificationsList.querySelector('.text-center');
            
            if (emptyState) {
                emptyState.remove();
            }

            const notificationHtml = `
                <div class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer" onclick="window.location.href='${data.url}'">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-briefcase text-blue-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">${data.message}</p>
                            <p class="text-sm text-gray-600">${data.job.title} at ${data.job.company}</p>
                            <p class="text-xs text-gray-500">Posted by ${data.job.posted_by} • ${data.job.created_at}</p>
                        </div>
                    </div>
                </div>
            `;
            
            notificationsList.insertAdjacentHTML('afterbegin', notificationHtml);

            // Show browser notification if permission granted
            if (Notification.permission === 'granted') {
                new Notification('New Job Posting', {
                    body: `${data.job.title} at ${data.job.company}`,
                    icon: '/favicon.ico'
                });
            }
        }

        function checkForNewNotifications() {
            // Simple polling fallback for when WebSockets aren't available
            fetch('/admin/notifications/check')
                .then(response => response.json())
                .then(data => {
                    if (data.hasNewNotifications) {
                        // Reload the page to show new notifications
                        window.location.reload();
                    }
                })
                .catch(error => console.log('Notification check failed:', error));
        }

        // Request notification permission
        if (Notification.permission === 'default') {
            Notification.requestPermission();
        }
        
        function loadNotifications() {
            // Load recent notifications from the server
            fetch('/admin/notifications/check')
                .then(response => response.json())
                .then(data => {
                    const notificationsList = document.getElementById('notifications-list');
                    const badge = document.getElementById('notification-badge');
                    
                    if (data.pendingCount > 0) {
                        // Show badge
                        badge.textContent = data.pendingCount;
                        badge.classList.remove('hidden');
                        
                        // Update notifications list
                        notificationsList.innerHTML = `
                            <div class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer" onclick="window.location.href='{{ route('admin.job-review') }}'">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-clock text-yellow-600 text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">${data.pendingCount} job posting${data.pendingCount > 1 ? 's' : ''} pending review</p>
                                        <p class="text-sm text-gray-600">Click to review pending job postings</p>
                                        <p class="text-xs text-gray-500">Just now</p>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        // Hide badge
                        badge.classList.add('hidden');
                        
                        // Show empty state
                        notificationsList.innerHTML = `
                            <div class="p-4 text-center text-gray-500">
                                <i class="fas fa-bell-slash text-2xl mb-2"></i>
                                <p>No new notifications</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.log('Failed to load notifications:', error);
                });
        }
        @endif

        // Close sidebar on window resize if mobile
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebar-overlay');
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('hidden');
            }
        });

        // Simple initialization
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure sidebar is visible on desktop
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full');
            }
            
            // Restore sidebar fold state
            restoreSidebarState();
        });
    </script>

    @stack('scripts')
</body>
</html>
