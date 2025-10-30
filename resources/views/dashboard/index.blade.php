@extends('layouts.app')

@section('content')
<!-- University Header Section -->
<div class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 relative overflow-hidden" style="background-image: url('{{ asset('images/balubals.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: fixed; height: 100svh; image-rendering: high-quality; image-rendering: -webkit-optimize-contrast; image-rendering: crisp-edges; backface-visibility: hidden; transform: translateZ(0);">
    <!-- Dark overlay for better text readability -->
    <div class="absolute inset-0 gray bg-opacity-50"></div>
    
    <!-- University Pattern Background -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><path d="M50 0L100 25v50L50 100L0 75V25z"/></g></svg>');"></div>
    </div>
    
    <!-- University Logo and Title -->
    <div class="relative z-10 px-4 h-full flex flex-col justify-center">
        <div class="max-w-5xl mx-auto">
            <div class="text-center text-white mb-6">
                <!-- University Logo -->
                <div class="flex justify-center mb-4">
                    <div class="bg-white/20 backdrop-blur-sm rounded-full p-3 border-2 border-white/30">
                        <!-- Replace with your USTP logo -->
                        <img src="{{ asset('images/alumni.png') }}" alt="USTP Logo" class="w-12 h-12 object-contain">
                        <!-- Fallback icon if logo not found -->
                        <i class="fas fa-university text-3xl text-white" style="display: none;"></i>
                    </div>
                </div>
                
                <!-- University Name -->
                <h1 class="text-2xl md:text-3xl font-bold mb-2 tracking-wide text-yellow-400" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.8);">
                    Alumni USTP Balubal Portal
                </h1>
                
                <!-- System Title -->
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20 max-w-2xl mx-auto">
                    <h2 class="text-xl md:text-2xl font-bold mb-2 text-yellow-400" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.8);">
                        From Campus to Career
                    </h2>
                    <p class="text-sm text-blue-100 leading-relaxed" style="text-shadow: 1px 1px 3px rgba(0,0,0,0.8);">
                        Tracking the professional's pathway of recent USTP Balubal graduates
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-2 justify-center items-center mb-6">
                @guest
                    <a href="{{ route('login') }}" class="bg-yellow-500 hover:bg-yellow-600 text-blue-900 px-4 py-2 rounded-lg text-sm font-bold transition-all duration-300 transform hover:scale-105 shadow-xl border-2 border-yellow-400">
                        <i class="fas fa-sign-in-alt mr-1"></i>
                        Access Portal
                    </a>
                    <a href="mailto:admin@ustp.edu.ph" class="bg-transparent border-2 border-white text-white hover:bg-white hover:text-blue-900 px-4 py-2 rounded-lg text-sm font-bold transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-envelope mr-1"></i>
                        Contact Admin
                    </a>
                @else
                    @if(auth()->user()->isGraduate())
                        <a href="{{ route('graduate.dashboard') }}" class="bg-yellow-500 hover:bg-yellow-600 text-blue-900 px-4 py-2 rounded-lg text-sm font-bold transition-all duration-300 transform hover:scale-105 shadow-xl border-2 border-yellow-400">
                            <i class="fas fa-graduation-cap mr-1"></i>
                            Graduate Portal
                        </a>
                    @elseif(auth()->user()->isStaff())
                        <a href="{{ route('staff.dashboard') }}" class="bg-yellow-500 hover:bg-yellow-600 text-blue-900 px-4 py-2 rounded-lg text-sm font-bold transition-all duration-300 transform hover:scale-105 shadow-xl border-2 border-yellow-400">
                            <i class="fas fa-users mr-1"></i>
                            Staff Portal
                        </a>
                    @elseif(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="bg-yellow-500 hover:bg-yellow-600 text-blue-900 px-4 py-2 rounded-lg text-sm font-bold transition-all duration-300 transform hover:scale-105 shadow-xl border-2 border-yellow-400">
                            <i class="fas fa-cog mr-1"></i>
                            Admin Portal
                        </a>
                    @endif
                @endguest
            </div>

            <!-- University Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 max-w-3xl mx-auto">
                <div class="bg-white/15 backdrop-blur-sm rounded-lg p-3 border border-white/25 text-center">
                    <div class="text-lg font-bold text-yellow-400 mb-1">{{ $stats['total_graduates'] ?? '500+' }}</div>
                    <div class="text-blue-200 font-medium text-xs">Graduates Tracked</div>
                </div>
                <div class="bg-white/15 backdrop-blur-sm rounded-lg p-3 border border-white/25 text-center">
                    <div class="text-lg font-bold text-yellow-400 mb-1">{{ $stats['employment_rate'] ?? '85' }}%</div>
                    <div class="text-blue-200 font-medium text-xs">Employment Rate</div>
                </div>
                <div class="bg-white/15 backdrop-blur-sm rounded-lg p-3 border border-white/25 text-center">
                    <div class="text-lg font-bold text-yellow-400 mb-1">{{ $stats['total_jobs'] ?? '200+' }}</div>
                    <div class="text-blue-200 font-medium text-xs">Job Opportunities</div>
                </div>
                <div class="bg-white/15 backdrop-blur-sm rounded-lg p-3 border border-white/25 text-center">
                    <div class="text-lg font-bold text-yellow-400 mb-1">15+</div>
                    <div class="text-blue-200 font-medium text-xs">Industry Partners</div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection

@push('scripts')
<script>
    // On small screens, disable fixed backgrounds to avoid janky scroll and layout issues
    (function() {
        const root = document.currentScript?.closest('body') || document.body;
        const hero = root.querySelector('[style*="balubals.jpg"]');
        if (!hero) return;
        function adjustBgAttachment() {
            if (window.innerWidth < 768) {
                hero.style.backgroundAttachment = 'scroll';
                hero.style.height = '100svh';
            } else {
                hero.style.backgroundAttachment = 'fixed';
                hero.style.height = '100vh';
            }
        }
        adjustBgAttachment();
        window.addEventListener('resize', adjustBgAttachment);
    })();
    // Prevent mobile overscroll from adding extra space
    document.documentElement.style.height = '100%';
    document.body.style.minHeight = '100svh';
</script>
@endpush