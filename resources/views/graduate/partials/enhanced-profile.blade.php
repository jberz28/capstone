@extends('layouts.dashboard')

@section('title', 'Complete Profile')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-display font-bold text-gradient mb-3">Complete Profile</h1>
        <p class="text-lg text-gray-600 font-body">Complete your profile to help us better understand your current status and career goals</p>
    </div>

    <!-- Profile Picture Section -->
    <div class="card-enhanced p-8 mb-8">
        <h3 class="text-2xl font-display font-bold text-gray-900 mb-6">Profile Picture</h3>
        <div class="flex items-center space-x-8">
            <div id="enhanced-avatar-container" class="w-32 h-32 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center overflow-hidden shadow-lg">
                @if($graduate->profile_picture)
                    <img src="{{ \Storage::url($graduate->profile_picture) }}" alt="Profile Picture" class="w-full h-full object-cover" id="profile-picture-preview">
                @else
                    <i class="fas fa-user text-white text-3xl"></i>
                @endif
            </div>
            <div>
                <input type="file" id="profile-picture-input" accept="image/*" class="hidden">
                <button onclick="document.getElementById('profile-picture-input').click()" class="btn-primary px-6 py-3">
                    <i class="fas fa-camera mr-2"></i>Change Picture
                </button>
                <p class="text-sm text-gray-500 mt-3 font-body">JPG, PNG or GIF. Max size 2MB.</p>
            </div>
        </div>
    </div>

    <!-- Status Overview Card -->
    <div class="card-enhanced p-8 mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500">
        <h3 class="text-2xl font-display font-bold text-gray-900 mb-6">Current Status Overview</h3>
        <div class="flex items-center space-x-6">
            <div class="flex-shrink-0">
                <span class="badge-enhanced text-lg
                    @if($graduate->current_status === 'employed') bg-green-100 text-green-800
                    @elseif($graduate->current_status === 'unemployed') bg-red-100 text-red-800
                    @elseif($graduate->current_status === 'undergraduate') bg-blue-100 text-blue-800
                    @elseif($graduate->current_status === 'pursuing_higher_education') bg-purple-100 text-purple-800
                    @elseif($graduate->current_status === 'self_employed') bg-yellow-100 text-yellow-800
                    @else bg-gray-100 text-gray-800 @endif">
                    <i class="fas fa-circle mr-2 text-xs"></i>
                    {{ $graduate->current_status_label ?? 'Graduate' }}
                </span>
            </div>
            <div class="flex-1">
                @if($graduate->current_status === 'employed' && $graduate->current_position)
                    <p class="text-lg text-gray-700 font-body">{{ $graduate->current_position }} at {{ $graduate->current_company ?? 'Company' }}</p>
                @elseif($graduate->current_status === 'pursuing_higher_education' && $graduate->pursuing_degree)
                    <p class="text-lg text-gray-700 font-body">Pursuing {{ $graduate->pursuing_degree }} at {{ $graduate->institution_name ?? 'Institution' }}</p>
                @elseif($graduate->current_status === 'undergraduate')
                    <p class="text-lg text-gray-700 font-body">Currently studying {{ $graduate->program ?? 'Program' }}</p>
                @else
                    <p class="text-lg text-gray-700 font-body">Update your profile to show more details</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <form id="enhanced-profile-form" method="POST" action="{{ route('graduate.profile.update') }}" class="space-y-6">
        @csrf
        
        <!-- Current Status Section -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-user-check text-blue-600 mr-2"></i>
                Current Status
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="current_status" class="block text-sm font-medium text-gray-700 mb-2">What best describes your current situation?</label>
                    <select name="current_status" id="current_status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" onchange="toggleStatusFields(this.value)">
                        <option value="graduate" {{ $graduate->current_status === 'graduate' ? 'selected' : '' }}>Graduate (Not currently employed or studying)</option>
                        <option value="undergraduate" {{ $graduate->current_status === 'undergraduate' ? 'selected' : '' }}>Undergraduate Student</option>
                        <option value="employed" {{ $graduate->current_status === 'employed' ? 'selected' : '' }}>Employed</option>
                        <option value="unemployed" {{ $graduate->current_status === 'unemployed' ? 'selected' : '' }}>Unemployed (Looking for work)</option>
                        <option value="pursuing_higher_education" {{ $graduate->current_status === 'pursuing_higher_education' ? 'selected' : '' }}>Pursuing Higher Education</option>
                        <option value="self_employed" {{ $graduate->current_status === 'self_employed' ? 'selected' : '' }}>Self-Employed</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Employment Information (shown when employed or self-employed) -->
        <div id="employment-section" class="bg-white rounded-lg shadow p-6" style="display: {{ in_array($graduate->current_status, ['employed', 'self_employed']) ? 'block' : 'none' }}">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-briefcase text-green-600 mr-2"></i>
                Employment Information
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="employment_type" class="block text-sm font-medium text-gray-700 mb-2">Employment Type</label>
                    <select name="employment_type" id="employment_type" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Type</option>
                        <option value="full_time" {{ $graduate->employment_type === 'full_time' ? 'selected' : '' }}>Full-time</option>
                        <option value="part_time" {{ $graduate->employment_type === 'part_time' ? 'selected' : '' }}>Part-time</option>
                        <option value="contract" {{ $graduate->employment_type === 'contract' ? 'selected' : '' }}>Contract</option>
                        <option value="freelance" {{ $graduate->employment_type === 'freelance' ? 'selected' : '' }}>Freelance</option>
                        <option value="internship" {{ $graduate->employment_type === 'internship' ? 'selected' : '' }}>Internship</option>
                        <option value="self_employed" {{ $graduate->employment_type === 'self_employed' ? 'selected' : '' }}>Self-employed</option>
                    </select>
                </div>
                
                <div>
                    <label for="job_level" class="block text-sm font-medium text-gray-700 mb-2">Job Level</label>
                    <select name="job_level" id="job_level" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Level</option>
                        <option value="entry" {{ $graduate->job_level === 'entry' ? 'selected' : '' }}>Entry Level</option>
                        <option value="mid" {{ $graduate->job_level === 'mid' ? 'selected' : '' }}>Mid Level</option>
                        <option value="senior" {{ $graduate->job_level === 'senior' ? 'selected' : '' }}>Senior Level</option>
                        <option value="manager" {{ $graduate->job_level === 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="director" {{ $graduate->job_level === 'director' ? 'selected' : '' }}>Director</option>
                        <option value="executive" {{ $graduate->job_level === 'executive' ? 'selected' : '' }}>Executive</option>
                    </select>
                </div>
                
                <div>
                    <label for="current_position" class="block text-sm font-medium text-gray-700 mb-2">Job Title / Position</label>
                    <input type="text" name="current_position" id="current_position" value="{{ $graduate->current_position ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., Software Developer, Marketing Manager">
                </div>
                
                <div>
                    <label for="current_company" class="block text-sm font-medium text-gray-700 mb-2">Company / Organization</label>
                    <input type="text" name="current_company" id="current_company" value="{{ $graduate->current_company ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., Tech Company Inc., Government Agency">
                </div>
                
                <div>
                    <label for="employment_sector" class="block text-sm font-medium text-gray-700 mb-2">Industry / Sector</label>
                    <select name="employment_sector" id="employment_sector" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Sector</option>
                        <option value="technology" {{ $graduate->employment_sector === 'technology' ? 'selected' : '' }}>Technology</option>
                        <option value="finance" {{ $graduate->employment_sector === 'finance' ? 'selected' : '' }}>Finance</option>
                        <option value="healthcare" {{ $graduate->employment_sector === 'healthcare' ? 'selected' : '' }}>Healthcare</option>
                        <option value="education" {{ $graduate->employment_sector === 'education' ? 'selected' : '' }}>Education</option>
                        <option value="government" {{ $graduate->employment_sector === 'government' ? 'selected' : '' }}>Government</option>
                        <option value="manufacturing" {{ $graduate->employment_sector === 'manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                        <option value="retail" {{ $graduate->employment_sector === 'retail' ? 'selected' : '' }}>Retail</option>
                        <option value="non_profit" {{ $graduate->employment_sector === 'non_profit' ? 'selected' : '' }}>Non-profit</option>
                        <option value="private" {{ $graduate->employment_sector === 'private' ? 'selected' : '' }}>Private Sector</option>
                        <option value="other" {{ $graduate->employment_sector === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                
                <div>
                    <label for="work_location" class="block text-sm font-medium text-gray-700 mb-2">Work Location</label>
                    <input type="text" name="work_location" id="work_location" value="{{ $graduate->work_location ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., Cagayan de Oro City, Remote">
                </div>
                
                <div>
                    <label for="employment_start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="employment_start_date" id="employment_start_date" value="{{ $graduate->employment_start_date ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="is_remote_work" class="block text-sm font-medium text-gray-700 mb-2">Work Arrangement</label>
                    <select name="is_remote_work" id="is_remote_work" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="0" {{ !$graduate->is_remote_work ? 'selected' : '' }}>On-site</option>
                        <option value="1" {{ $graduate->is_remote_work ? 'selected' : '' }}>Remote</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-6">
                <label for="job_description" class="block text-sm font-medium text-gray-700 mb-2">Job Description</label>
                <textarea name="job_description" id="job_description" rows="4" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Describe your current role, responsibilities, and key achievements">{{ $graduate->job_description ?? '' }}</textarea>
            </div>
        </div>

        <!-- Education Information (shown when pursuing higher education) -->
        <div id="education-section" class="bg-white rounded-lg shadow p-6" style="display: {{ $graduate->current_status === 'pursuing_higher_education' ? 'block' : 'none' }}">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-graduation-cap text-purple-600 mr-2"></i>
                Higher Education Information
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="pursuing_degree" class="block text-sm font-medium text-gray-700 mb-2">Degree Program</label>
                    <input type="text" name="pursuing_degree" id="pursuing_degree" value="{{ $graduate->pursuing_degree ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., Master of Science in Computer Science">
                </div>
                
                <div>
                    <label for="institution_name" class="block text-sm font-medium text-gray-700 mb-2">Institution</label>
                    <input type="text" name="institution_name" id="institution_name" value="{{ $graduate->institution_name ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., University of the Philippines">
                </div>
                
                <div>
                    <label for="expected_graduation" class="block text-sm font-medium text-gray-700 mb-2">Expected Graduation</label>
                    <input type="date" name="expected_graduation" id="expected_graduation" value="{{ $graduate->expected_graduation ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- Career Development Section -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-chart-line text-orange-600 mr-2"></i>
                Career Development
            </h3>
            
            <div class="space-y-6">
                <div>
                    <label for="career_goals" class="block text-sm font-medium text-gray-700 mb-2">Career Goals & Aspirations</label>
                    <textarea name="career_goals" id="career_goals" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Describe your career aspirations, where you see yourself in 5 years, and your professional goals">{{ $graduate->career_goals ?? '' }}</textarea>
                </div>
                
                <div>
                    <label for="skills" class="block text-sm font-medium text-gray-700 mb-2">Key Skills & Competencies</label>
                    <textarea name="skills" id="skills" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                              placeholder="List your key skills, technical competencies, programming languages, software tools, etc.">{{ $graduate->skills ?? '' }}</textarea>
                </div>
                
                <div>
                    <label for="interests" class="block text-sm font-medium text-gray-700 mb-2">Professional Interests</label>
                    <textarea name="interests" id="interests" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Describe your professional interests, areas of focus, and what you're passionate about in your field">{{ $graduate->interests ?? '' }}</textarea>
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-id-card text-purple-600 mr-2"></i>
                Personal Information
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                    <input type="text" name="first_name" id="first_name" value="{{ $graduate->first_name ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter first name">
                </div>
                
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                    <input type="text" name="last_name" id="last_name" value="{{ $graduate->last_name ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter last name">
                </div>
                
                <div>
                    <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
                    <input type="text" name="middle_name" id="middle_name" value="{{ $graduate->middle_name ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter middle name">
                </div>
                
                <div>
                    <label for="middle_initial" class="block text-sm font-medium text-gray-700 mb-2">Middle Initial</label>
                    <input type="text" name="middle_initial" id="middle_initial" value="{{ $graduate->middle_initial ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter middle initial" maxlength="1">
                </div>
                
                <div>
                    <label for="extension" class="block text-sm font-medium text-gray-700 mb-2">Name Extension (Jr., Sr., III, etc.)</label>
                    <input type="text" name="extension" id="extension" value="{{ $graduate->extension ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., Jr., Sr., III">
                </div>
                
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                    <select name="gender" id="gender" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Gender</option>
                        <option value="male" {{ $graduate->gender === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ $graduate->gender === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ $graduate->gender === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                    <input type="date" name="birth_date" id="birth_date" value="{{ $graduate->birth_date ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="place_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Place of Birth</label>
                    <input type="text" name="place_of_birth" id="place_of_birth" value="{{ $graduate->place_of_birth ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter place of birth">
                </div>
                
                <div>
                    <label for="civil_status" class="block text-sm font-medium text-gray-700 mb-2">Civil Status</label>
                    <select name="civil_status" id="civil_status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Civil Status</option>
                        <option value="single" {{ $graduate->civil_status === 'single' ? 'selected' : '' }}>Single</option>
                        <option value="married" {{ $graduate->civil_status === 'married' ? 'selected' : '' }}>Married</option>
                        <option value="widowed" {{ $graduate->civil_status === 'widowed' ? 'selected' : '' }}>Widowed</option>
                        <option value="divorced" {{ $graduate->civil_status === 'divorced' ? 'selected' : '' }}>Divorced</option>
                        <option value="separated" {{ $graduate->civil_status === 'separated' ? 'selected' : '' }}>Separated</option>
                    </select>
                </div>
                
                <div>
                    <label for="nationality" class="block text-sm font-medium text-gray-700 mb-2">Nationality</label>
                    <input type="text" name="nationality" id="nationality" value="{{ $graduate->nationality ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter nationality">
                </div>
                
                <div>
                    <label for="religion" class="block text-sm font-medium text-gray-700 mb-2">Religion</label>
                    <input type="text" name="religion" id="religion" value="{{ $graduate->religion ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter religion">
                </div>
                
                <div>
                    <label for="blood_type" class="block text-sm font-medium text-gray-700 mb-2">Blood Type</label>
                    <select name="blood_type" id="blood_type" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Blood Type</option>
                        <option value="A+" {{ $graduate->blood_type === 'A+' ? 'selected' : '' }}>A+</option>
                        <option value="A-" {{ $graduate->blood_type === 'A-' ? 'selected' : '' }}>A-</option>
                        <option value="B+" {{ $graduate->blood_type === 'B+' ? 'selected' : '' }}>B+</option>
                        <option value="B-" {{ $graduate->blood_type === 'B-' ? 'selected' : '' }}>B-</option>
                        <option value="AB+" {{ $graduate->blood_type === 'AB+' ? 'selected' : '' }}>AB+</option>
                        <option value="AB-" {{ $graduate->blood_type === 'AB-' ? 'selected' : '' }}>AB-</option>
                        <option value="O+" {{ $graduate->blood_type === 'O+' ? 'selected' : '' }}>O+</option>
                        <option value="O-" {{ $graduate->blood_type === 'O-' ? 'selected' : '' }}>O-</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Family Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-users text-green-600 mr-2"></i>
                Family Information
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="father_name" class="block text-sm font-medium text-gray-700 mb-2">Father's Name</label>
                    <input type="text" name="father_name" id="father_name" value="{{ $graduate->father_name ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter father's full name">
                </div>
                
                <div>
                    <label for="mother_name" class="block text-sm font-medium text-gray-700 mb-2">Mother's Name</label>
                    <input type="text" name="mother_name" id="mother_name" value="{{ $graduate->mother_name ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter mother's full name">
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-map-marker-alt text-orange-600 mr-2"></i>
                Address Information
            </h3>
            
            <div class="space-y-6">
                <div>
                    <h4 class="text-md font-medium text-gray-800 mb-3">Present Address</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="present_address" class="block text-sm font-medium text-gray-700 mb-2">Complete Address</label>
                            <textarea name="present_address" id="present_address" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Enter complete present address">{{ $graduate->present_address ?? '' }}</textarea>
                        </div>
                        <div>
                            <label for="municipality_city" class="block text-sm font-medium text-gray-700 mb-2">City/Municipality</label>
                            <input type="text" name="municipality_city" id="municipality_city" value="{{ $graduate->municipality_city ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Enter city or municipality">
                        </div>
                        <div>
                            <label for="province_region" class="block text-sm font-medium text-gray-700 mb-2">Province/Region</label>
                            <input type="text" name="province_region" id="province_region" value="{{ $graduate->province_region ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Enter province or region">
                        </div>
                        <div>
                            <label for="barangay" class="block text-sm font-medium text-gray-700 mb-2">Barangay</label>
                            <input type="text" name="barangay" id="barangay" value="{{ $graduate->barangay ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Enter barangay">
                        </div>
                        <div>
                            <label for="zip_code" class="block text-sm font-medium text-gray-700 mb-2">ZIP Code</label>
                            <input type="text" name="zip_code" id="zip_code" value="{{ $graduate->zip_code ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Enter ZIP code">
                        </div>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-md font-medium text-gray-800 mb-3">Permanent Address</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="permanent_address" class="block text-sm font-medium text-gray-700 mb-2">Complete Address</label>
                            <textarea name="permanent_address" id="permanent_address" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Enter complete permanent address">{{ $graduate->permanent_address ?? '' }}</textarea>
                        </div>
                        <div>
                            <label for="permanent_city" class="block text-sm font-medium text-gray-700 mb-2">City/Municipality</label>
                            <input type="text" name="permanent_city" id="permanent_city" value="{{ $graduate->permanent_city ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Enter city or municipality">
                        </div>
                        <div>
                            <label for="permanent_province" class="block text-sm font-medium text-gray-700 mb-2">Province/Region</label>
                            <input type="text" name="permanent_province" id="permanent_province" value="{{ $graduate->permanent_province ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Enter province or region">
                        </div>
                        <div>
                            <label for="permanent_barangay" class="block text-sm font-medium text-gray-700 mb-2">Barangay</label>
                            <input type="text" name="permanent_barangay" id="permanent_barangay" value="{{ $graduate->permanent_barangay ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Enter barangay">
                        </div>
                        <div>
                            <label for="permanent_zip_code" class="block text-sm font-medium text-gray-700 mb-2">ZIP Code</label>
                            <input type="text" name="permanent_zip_code" id="permanent_zip_code" value="{{ $graduate->permanent_zip_code ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Enter ZIP code">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Information (always shown) -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-university text-blue-600 mr-2"></i>
                Academic Information
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">Student ID</label>
                    <input type="text" name="student_id" id="student_id" value="{{ $graduate->student_id ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., 2020-12345">
                </div>
                
                <div>
                    <label for="program" class="block text-sm font-medium text-gray-700 mb-2">Program</label>
                    <select name="program" id="program" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Program</option>
                        <option value="Bachelor of Science in Information Technology" {{ $graduate->program === 'Bachelor of Science in Information Technology' ? 'selected' : '' }}>Bachelor of Science in Information Technology</option>
                        <option value="Bachelor of Science in Computer Science" {{ $graduate->program === 'Bachelor of Science in Computer Science' ? 'selected' : '' }}>Bachelor of Science in Computer Science</option>
                        <option value="Bachelor of Science in Information Systems" {{ $graduate->program === 'Bachelor of Science in Information Systems' ? 'selected' : '' }}>Bachelor of Science in Information Systems</option>
                        <option value="Bachelor of Science in Computer Engineering" {{ $graduate->program === 'Bachelor of Science in Computer Engineering' ? 'selected' : '' }}>Bachelor of Science in Computer Engineering</option>
                    </select>
                </div>
                
                <div>
                    <label for="batch_year" class="block text-sm font-medium text-gray-700 mb-2">Batch Year</label>
                    <input type="number" name="batch_year" id="batch_year" value="{{ $graduate->batch_year ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., 2024" min="2000" max="2030">
                </div>
                
                <div>
                    <label for="graduation_year" class="block text-sm font-medium text-gray-700 mb-2">Year Graduated</label>
                    <input type="number" name="graduation_year" id="graduation_year" value="{{ $graduate->graduation_year ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., 2023" min="1950" max="2030">
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-address-book text-green-600 mr-2"></i>
                Contact Information
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-2">Contact Number</label>
                    <input type="text" name="contact_number" id="contact_number" value="{{ $graduate->contact_number ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., +63 912 345 6789">
                </div>
                
                <div>
                    <label for="linkedin_profile" class="block text-sm font-medium text-gray-700 mb-2">LinkedIn Profile</label>
                    <input type="url" name="linkedin_profile" id="linkedin_profile" value="{{ $graduate->linkedin_profile ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="https://linkedin.com/in/yourprofile">
                </div>
            </div>
            
            <div class="mt-6">
                <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Bio / About Me</label>
                <textarea name="bio" id="bio" rows="4" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Tell us about yourself, your background, and what makes you unique">{{ $graduate->bio ?? '' }}</textarea>
            </div>
        </div>

        <!-- Password Change Section -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-lock text-red-600 mr-2"></i>
                Change Password
            </h3>
            
            <form id="password-change-form" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                        <input type="password" name="current_password" id="current_password" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter current password">
                    </div>
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <input type="password" name="new_password" id="new_password" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter new password">
                    </div>
                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Confirm new password">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors font-medium">
                        <i class="fas fa-key mr-2"></i>Change Password
                    </button>
                </div>
            </form>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" form="enhanced-profile-form" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                <i class="fas fa-save mr-2"></i>Save Profile Information
            </button>
        </div>
    </form>
</div>

<script>
// Toggle status-specific fields
function toggleStatusFields(status) {
    const employmentSection = document.getElementById('employment-section');
    const educationSection = document.getElementById('education-section');
    
    // Hide all sections first
    employmentSection.style.display = 'none';
    educationSection.style.display = 'none';
    
    // Show relevant sections based on status
    if (status === 'employed' || status === 'self_employed') {
        employmentSection.style.display = 'block';
    } else if (status === 'pursuing_higher_education') {
        educationSection.style.display = 'block';
    }
}

// Profile picture upload
document.getElementById('profile-picture-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Immediate local preview while upload happens
        try {
            const localUrl = URL.createObjectURL(file);
            const container = document.getElementById('enhanced-avatar-container');
            let preview = document.getElementById('profile-picture-preview');
            if (preview) {
                preview.src = localUrl;
            } else if (container) {
                container.innerHTML = `<img src="${localUrl}" alt="Profile Picture" class="w-full h-full object-cover" id="profile-picture-preview">`;
            }
        } catch (err) {
            console.warn('Local preview failed:', err);
        }

        const formData = new FormData();
        formData.append('profile_picture', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        fetch('{{ route("graduate.profile.picture") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let cacheBustedUrl = data.profile_picture_url + '?t=' + new Date().getTime();
                // Normalize host to current origin in case APP_URL is misconfigured
                try {
                    const u = new URL(cacheBustedUrl);
                    cacheBustedUrl = window.location.origin + u.pathname + (u.search || '');
                } catch (_) {
                    // ignore if URL parsing fails; use original
                }
                const preview = document.getElementById('profile-picture-preview');
                const container = document.getElementById('enhanced-avatar-container');

                const applyAsBackground = () => {
                    if (container) {
                        container.innerHTML = '';
                        container.style.backgroundImage = `url('${cacheBustedUrl}')`;
                        container.style.backgroundSize = 'cover';
                        container.style.backgroundPosition = 'center';
                    }
                };

                const img = new Image();
                img.onload = () => {
                    if (preview) {
                        preview.src = cacheBustedUrl;
                    } else if (container) {
                        container.innerHTML = '';
                        img.className = 'w-full h-full object-cover';
                        img.id = 'profile-picture-preview';
                        container.appendChild(img);
                    }
                };
                img.onerror = () => {
                    console.error('Profile image failed to load:', cacheBustedUrl);
                    applyAsBackground();
                };
                img.src = cacheBustedUrl;

                // Also refresh header avatar if present
                const headerProfilePicture = document.getElementById('header-profile-picture');
                if (headerProfilePicture) {
                    const existingImg = headerProfilePicture.querySelector('img');
                    const existingIcon = headerProfilePicture.querySelector('i');
                    if (existingImg) {
                        existingImg.src = cacheBustedUrl;
                    } else if (existingIcon) {
                        existingIcon.style.display = 'none';
                        const newImg = document.createElement('img');
                        newImg.src = cacheBustedUrl;
                        newImg.alt = 'Profile Picture';
                        newImg.className = 'w-full h-full object-cover';
                        headerProfilePicture.appendChild(newImg);
                    }
                }
                alert('Profile picture updated successfully!');
            } else {
                alert('Error: ' + (data.message || 'Failed to upload profile picture'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error uploading profile picture. Please try again.');
        });
    }
});

// Form submission with loading state
document.getElementById('enhanced-profile-form').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
    submitBtn.disabled = true;
    
    // Re-enable button after 5 seconds as fallback
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 5000);
});

// Password change form submission
document.getElementById('password-change-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Changing...';
    submitBtn.disabled = true;
    
    fetch('{{ route("graduate.profile.change-password") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Password changed successfully!');
            // Clear the form
            this.reset();
        } else {
            let errorMessage = data.message || 'Error changing password';
            if (data.errors) {
                errorMessage += '\n\nValidation errors:\n';
                for (const field in data.errors) {
                    errorMessage += `- ${field}: ${data.errors[field].join(', ')}\n`;
                }
            }
            alert(errorMessage);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error changing password. Please try again.');
    })
    .finally(() => {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});
</script>
@endsection
