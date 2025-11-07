<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Profile Status Card -->
    <div class="card-enhanced p-6">
        <div class="flex items-center">
            <div class="p-4 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg relative overflow-hidden">
                <div class="absolute top-0 right-0 w-8 h-8 bg-white bg-opacity-20 rounded-full -mr-2 -mt-2"></div>
                <div class="absolute bottom-0 left-0 w-6 h-6 bg-white bg-opacity-10 rounded-full -ml-1 -mb-1"></div>
                <div class="relative z-10 text-center">
                    @if($graduate && $graduate->current_status)
                        <div class="text-white text-3xl font-bold">👤✓</div>
                        <div class="text-white text-xs mt-1">Profile</div>
                    @else
                        <div class="text-white text-3xl font-bold">👤✏️</div>
                        <div class="text-white text-xs mt-1">Profile</div>
                    @endif
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 font-body">Profile Status</p>
                <p class="text-2xl font-display font-bold text-gray-900">{{ $graduate ? 'Complete' : 'Incomplete' }}</p>
                @if($graduate && $graduate->current_status)
                    <p class="text-xs text-green-600 font-body mt-1">
                        <i class="fas fa-check-circle mr-1"></i>{{ $graduate->current_status_label }}
                    </p>
                @else
                    <p class="text-xs text-orange-600 font-body mt-1">
                        <i class="fas fa-exclamation-triangle mr-1"></i>Needs completion
                    </p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Resumes Card -->
    <div class="card-enhanced p-6">
        <div class="flex items-center">
            <div class="p-4 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl shadow-lg relative overflow-hidden">
                <div class="absolute top-0 right-0 w-8 h-8 bg-white bg-opacity-20 rounded-full -mr-2 -mt-2"></div>
                <div class="absolute bottom-0 left-0 w-6 h-6 bg-white bg-opacity-10 rounded-full -ml-1 -mb-1"></div>
                <div class="relative z-10 text-center">
                    @if($graduate && $graduate->resumes->count() > 0)
                        <div class="text-white text-3xl font-bold">📄</div>
                        <div class="text-white text-xs mt-1">Resume</div>
                    @else
                        <div class="text-white text-3xl font-bold">📝</div>
                        <div class="text-white text-xs mt-1">Resume</div>
                    @endif
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 font-body">Resumes</p>
                <p class="text-2xl font-display font-bold text-gray-900">{{ $graduate ? $graduate->resumes->count() : 0 }}</p>
                @if($graduate && $graduate->resumes->count() > 0)
                    <p class="text-xs text-green-600 font-body mt-1">
                        <i class="fas fa-check-circle mr-1"></i>{{ $graduate->resumes->count() }} resume(s) ready
                    </p>
                @else
                    <p class="text-xs text-orange-600 font-body mt-1">
                        <i class="fas fa-plus-circle mr-1"></i>Create your first resume
                    </p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Job Applications Card -->
    <div class="card-enhanced p-6">
        <div class="flex items-center">
            <div class="p-4 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-xl shadow-lg relative overflow-hidden">
                <div class="absolute top-0 right-0 w-8 h-8 bg-white bg-opacity-20 rounded-full -mr-2 -mt-2"></div>
                <div class="absolute bottom-0 left-0 w-6 h-6 bg-white bg-opacity-10 rounded-full -ml-1 -mb-1"></div>
                <div class="relative z-10 text-center">
                    <div class="text-white text-3xl font-bold">💼</div>
                    <div class="text-white text-xs mt-1">Jobs</div>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 font-body">Job Applications</p>
                <p class="text-2xl font-display font-bold text-gray-900">0</p>
                <p class="text-xs text-blue-600 font-body mt-1">
                    <i class="fas fa-rocket mr-1"></i>Start your job search
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="card-enhanced p-8 mb-8">
    <h2 class="text-2xl font-display font-bold text-gray-900 mb-6">Your Recent Activity</h2>
    <div class="space-y-4">
        <div class="flex items-center p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                <i class="fas fa-clock text-blue-600 text-sm"></i>
            </div>
            <div>
                <p class="font-display font-semibold text-gray-900">Profile Status</p>
                <p class="text-sm text-gray-600 font-body">Last updated: {{ $graduate ? $graduate->updated_at->diffForHumans() : 'Never' }}</p>
            </div>
        </div>
        
        @if($graduate && $graduate->resumes->count() > 0)
        <div class="flex items-center p-4 bg-green-50 rounded-lg border-l-4 border-green-500">
            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-4">
                <i class="fas fa-file-alt text-green-600 text-sm"></i>
            </div>
            <div>
                <p class="font-display font-semibold text-gray-900">Resume Portfolio</p>
                <p class="text-sm text-gray-600 font-body">You have {{ $graduate->resumes->count() }} resume(s) on file</p>
            </div>
        </div>
        @else
        <div class="flex items-center p-4 bg-yellow-50 rounded-lg border-l-4 border-yellow-500">
            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-sm"></i>
            </div>
            <div>
                <p class="font-display font-semibold text-gray-900">Resume Needed</p>
                <p class="text-sm text-gray-600 font-body">Consider generating your first resume</p>
            </div>
        </div>
        @endif
        
        <div class="flex items-center p-4 bg-purple-50 rounded-lg border-l-4 border-purple-500">
            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                <i class="fas fa-search text-purple-600 text-sm"></i>
            </div>
            <div>
                <p class="font-display font-semibold text-gray-900">Job Opportunities</p>
                <p class="text-sm text-gray-600 font-body">Explore new job opportunities</p>
            </div>
        </div>
    </div>
</div>

<!-- Alumni Activities -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Upcoming Alumni Activities -->
    <div class="card-enhanced p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-display font-bold text-gray-900">Upcoming Alumni Activities</h2>
            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-display font-semibold">View All</a>
        </div>
        
        @if($alumniActivities && $alumniActivities->count() > 0)
        <div class="space-y-4">
            @foreach($alumniActivities as $activity)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all duration-200 hover:border-blue-300">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-3">
                            <span class="badge-enhanced
                                @if($activity->type === 'homecoming') bg-purple-100 text-purple-800
                                @elseif($activity->type === 'reunion') bg-blue-100 text-blue-800
                                @elseif($activity->type === 'mentorship') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $activity->type_label }}
                            </span>
                            @if($activity->is_featured)
                            <span class="badge-enhanced bg-yellow-100 text-yellow-800">
                                <i class="fas fa-star mr-1"></i>Featured
                            </span>
                            @endif
                        </div>
                        <h3 class="text-lg font-display font-semibold text-gray-900 mb-2">{{ $activity->title }}</h3>
                        <p class="text-sm text-gray-600 font-body mb-3">{{ Str::limit($activity->description, 80) }}</p>
                        <div class="flex items-center space-x-4 text-xs text-gray-500 font-body">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $activity->formatted_event_date }}</span>
                            <span><i class="fas fa-map-marker-alt mr-1"></i>{{ $activity->location }}</span>
                            <span><i class="fas fa-tag mr-1"></i>{{ $activity->formatted_registration_fee }}</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        @if($activity->can_register)
                        <button class="btn-primary px-4 py-2 text-sm font-display font-semibold">
                            Register
                        </button>
                        @elseif($activity->is_full)
                        <span class="text-red-600 text-xs font-display font-semibold">Full</span>
                        @else
                        <span class="text-gray-500 text-xs font-body">Closed</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <i class="fas fa-calendar-alt text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No upcoming activities</h3>
            <p class="text-gray-500">Check back later for new alumni events and activities.</p>
        </div>
        @endif
    </div>

    <!-- Batch-Specific Activities -->
    <div class="card-enhanced p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-display font-bold text-gray-900">Your Batch Activities</h2>
            <span class="text-sm text-gray-500 font-body bg-blue-100 px-3 py-1 rounded-full">Class of {{ $graduate->batch_year ?? 'N/A' }}</span>
        </div>
        
        @if($batchActivities && $batchActivities->count() > 0)
        <div class="space-y-4">
            @foreach($batchActivities as $activity)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all duration-200 hover:border-green-300">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-3">
                            <span class="badge-enhanced bg-blue-100 text-blue-800">
                                <i class="fas fa-users mr-1"></i>Class of {{ $activity->batch_year }}
                            </span>
                        </div>
                        <h3 class="text-lg font-display font-semibold text-gray-900 mb-2">{{ $activity->title }}</h3>
                        <p class="text-sm text-gray-600 font-body mb-3">{{ Str::limit($activity->description, 80) }}</p>
                        <div class="flex items-center space-x-4 text-xs text-gray-500 font-body">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $activity->formatted_event_date }}</span>
                            <span><i class="fas fa-map-marker-alt mr-1"></i>{{ $activity->location }}</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        @if($activity->can_register)
                        <button class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-display font-semibold hover:bg-green-700 transition-colors">
                            Join
                        </button>
                        @elseif($activity->is_full)
                        <span class="text-red-600 text-xs font-display font-semibold">Full</span>
                        @else
                        <span class="text-gray-500 text-xs font-body">Closed</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No batch activities</h3>
            <p class="text-gray-500">No specific activities for your batch at the moment.</p>
        </div>
        @endif
    </div>
</div>

