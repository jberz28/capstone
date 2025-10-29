@extends('layouts.app')

@section('content')
<div id="login-bg" class="min-h-screen flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8 relative" style="background-image: url('{{ asset('images/balubals.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: fixed;">
    <!-- Dark overlay for better text readability -->
    <div class="absolute inset-0 bg-blue bg-opacity-50"></div>
    
    <div class="max-w-md w-full space-y-8 relative z-10">
        <div class="text-center">
            <div class="mx-auto h-20 w-20 bg-white rounded-full flex items-center justify-center mb-4 overflow-hidden">
                <img src="{{ asset('images/alumni.png') }}" alt="USTP Logo" class="w-full h-full object-contain">
            </div>
            <h2 class="text-3xl font-bold text-black mb-2">Alumni USTP Balubal Portal</h2>
            <p class="text-blue-200">Sign in to your account</p>
        </div>
        
        <div class="bg-white/95 backdrop-blur-sm rounded-lg shadow-xl p-8">
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2"></i>Email Address
                    </label>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                           value="{{ old('email') }}" placeholder="Enter your email">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                           placeholder="Enter your password">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember" type="checkbox" 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>
                </div>

                <div>
                    <button type="submit" 
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Sign In
                    </button>
                </div>

                <div class="text-center mt-2">
                    <p class="text-sm text-gray-600">
                        Need an account? Contact your administrator.
                    </p>
                </div>
            </form>
        </div>

        <!-- Demo Accounts -->
        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6 border border-white/20">
            <h3 class="text-lg font-semibold text-white mb-4 text-center">Demo Accounts</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center text-blue-200">
                    <span><i class="fas fa-user-shield mr-2"></i>Admin:</span>
                    <span class="font-mono">admin@ustp.edu.ph / password</span>
                </div>
                <div class="flex justify-between items-center text-blue-200">
                    <span><i class="fas fa-users mr-2"></i>Staff:</span>
                    <span class="font-mono">staff@ustp.edu.ph / password</span>
                </div>
                <div class="flex justify-between items-center text-blue-200">
                    <span><i class="fas fa-user-graduate mr-2"></i>Graduate:</span>
                    <span class="font-mono">graduate@ustp.edu.ph / password</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Disable fixed background on small screens to improve scroll/paint perf
    (function() {
        const el = document.getElementById('login-bg');
        function applyBgAttachment() {
            if (!el) return;
            if (window.innerWidth < 768) {
                el.style.backgroundAttachment = 'scroll';
            } else {
                el.style.backgroundAttachment = 'fixed';
            }
        }
        applyBgAttachment();
        window.addEventListener('resize', applyBgAttachment);
    })();
</script>
@endpush
@endsection