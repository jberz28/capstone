@extends('layouts.dashboard')

@section('page-title', 'Admin Dashboard')
@section('page-description', 'System overview and management')

@section('content')
<div id="content-area">
    <!-- Notification Alert for Pending Jobs -->
    @if($stats['pending_job_reviews'] > 0)
    <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">
                    {{ $stats['pending_job_reviews'] }} job posting{{ $stats['pending_job_reviews'] > 1 ? 's' : '' }} pending review
                </h3>
                <div class="mt-2">
                    <div class="text-sm text-yellow-700">
                        <p>New job postings are waiting for your review and approval.</p>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.job-postings') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-yellow-800 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            <span class="relative">
                                <i class="fas fa-clipboard-check mr-2"></i>
                                @if($stats['pending_job_reviews'] > 0)
                                  <span class="absolute top-0 right-0 block w-2 h-2 bg-red-500 rounded-full ring-2 ring-white" style="margin-top:-4px;margin-right:-6px;"></span>
                                @endif
                            </span>
                            Review Jobs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="card-enhanced p-6 shadow-xl transition-transform">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-500">Total Users</h3>
                    <p class="text-xl font-semibold text-gray-900">{{ $stats['total_users'] }}</p>
                </div>
            </div>
        </div>
        <div class="card-enhanced p-6 shadow-xl transition-transform">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-user-graduate text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-500">Total Graduates</h3>
                    <p class="text-xl font-semibold text-gray-900">{{ $stats['total_graduates'] }}</p>
                </div>
            </div>
        </div>
        <div class="card-enhanced p-6 shadow-xl transition-transform">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="fas fa-check-circle text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-500">Verified Graduates</h3>
                    <p class="text-xl font-semibold text-gray-900">{{ $stats['verified_graduates'] }}</p>
                </div>
            </div>
        </div>
        <div class="card-enhanced p-6 shadow-xl transition-transform">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <i class="fas fa-hourglass-half text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-500">Pending Verifications</h3>
                    <p class="text-xl font-semibold text-gray-900">{{ $stats['pending_verifications'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card-enhanced p-6 shadow-xl transition-transform">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Graduates</h2>
            @if($recent_graduates->isEmpty())
                <p class="text-gray-600">No recent graduates to display.</p>
            @else
                <ul class="divide-y divide-gray-200">
                    @foreach($recent_graduates as $graduate)
                        <li class="py-3 flex justify-between items-center">
                            <div>
                                <p class="text-lg font-semibold text-gray-900">{{ $graduate->user->name }}</p>
                                <p class="text-sm text-gray-600">{{ $graduate->program }} ({{ $graduate->batch_year }})</p>
                            </div>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $graduate->verification_status === 'verified' ? 'bg-green-100 text-green-800' : ($graduate->verification_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($graduate->verification_status) }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="card-enhanced p-6 shadow-xl transition-transform">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Job Postings</h2>
            @if($recent_job_postings->isEmpty())
                <p class="text-gray-600">No recent job postings to display.</p>
            @else
                <ul class="divide-y divide-gray-200">
                    @foreach($recent_job_postings as $job)
                        <li class="py-3">
                            <p class="text-lg font-semibold text-gray-900">{{ $job->title }} at {{ $job->company }}</p>
                            <p class="text-sm text-gray-600">{{ $job->location }} - {{ $job->employment_type }}</p>
                            <p class="text-xs text-gray-500">Posted by: {{ $job->postedBy->name }} on {{ $job->created_at->format('M d, Y') }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection