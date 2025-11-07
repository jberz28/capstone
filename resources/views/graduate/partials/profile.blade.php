<div class="grid grid-cols-1 lg:grid-cols-4 gap-4 lg:gap-6">
    <!-- Profile Sidebar -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-lg border border-gray-200 p-4 lg:p-6">
            <!-- Profile Picture -->
            <div class="text-center mb-6">
                <div class="w-24 h-24 bg-gray-200 rounded-full mx-auto mb-4 flex items-center justify-center overflow-hidden border-2 border-gray-300 shadow-sm">
                    @if($graduate && $graduate->profile_picture)
                        <img src="{{ \Storage::url($graduate->profile_picture) }}" alt="Profile Picture" class="w-full h-full object-cover">
                    @else
                        <i class="fas fa-user text-gray-400 text-3xl"></i>
                    @endif
                </div>
                <h3 class="text-lg font-semibold text-gray-900">{{ auth()->user()->name }}</h3>
                <p class="text-sm text-blue-600 font-medium">{{ $graduate->student_id ?? 'N/A' }}</p>
                <p class="text-sm text-blue-600 font-medium">{{ ucfirst(auth()->user()->role) }}</p>
                
                <!-- Upload Profile Picture Button -->
                <button onclick="document.getElementById('profile-picture-input').click()" class="mt-2 text-xs text-blue-600 hover:text-blue-800 underline">
                    Change Photo
                </button>
                <input type="file" id="profile-picture-input" accept="image/*" class="hidden" onchange="uploadProfilePicture(this)">
            </div>

            <!-- Profile Navigation -->
            <nav class="space-y-2">
                <a href="{{ route('graduate.student-info') }}" class="flex items-center px-4 py-3 text-gray-700 bg-blue-50 border-r-4 border-blue-600 rounded-l-lg shadow-sm hover:bg-blue-100 transition-colors">
                    <i class="fas fa-user mr-3 text-blue-600"></i>
                    <span class="font-medium">My Student Information</span>
                </a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="lg:col-span-3">
        <div class="bg-white rounded-lg shadow-lg border border-gray-200 p-4 lg:p-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Profile Management</h2>
                <p class="text-sm text-gray-600 mt-1">Manage your profile picture and information</p>
            </div>

            <!-- Profile Picture Section -->
            <div class="bg-gray-50 p-6 rounded-lg mb-6 border border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Profile Picture</h3>
                <div class="flex items-center space-x-6">
                    <div class="flex-shrink-0">
                        <div class="w-24 h-24 bg-yellow-400 rounded-full flex items-center justify-center overflow-hidden border-2 border-yellow-500 shadow-md">
                            @if($graduate && $graduate->profile_picture)
                                <img src="{{ \Storage::url($graduate->profile_picture) }}" alt="Profile Picture" class="w-full h-full object-cover" id="profile-picture-preview">
                            @else
                                <i class="fas fa-user text-blue-900 text-3xl"></i>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="mb-4">
                            <label for="profile_picture" class="block text-sm font-medium text-gray-700 mb-2">
                                Upload Profile Picture
                            </label>
                            <input type="file" id="profile_picture" name="profile_picture" accept="image/*" 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                   onchange="uploadProfilePicture(this)">
                        </div>
                        <p class="text-sm text-gray-500">
                            Recommended: Square image, at least 200x200 pixels. Max file size: 2MB
                        </p>
                    </div>
                </div>
            </div>

            <!-- Student Information -->
            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Student Information</h3>
                        <p class="text-sm text-gray-600 mt-1">Manage your academic and personal information</p>
                    </div>
                    <button onclick="toggleEditMode()" id="edit-toggle-btn" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Information
                    </button>
                </div>
                
                <form id="quick-info-form" method="POST" action="{{ route('graduate.student-info.update') }}" class="space-y-8">
                    @csrf
                    
                    <!-- Personal Identification Section -->
                    <div class="bg-white p-6 rounded-lg border border-gray-200">
                        <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-id-card mr-2 text-blue-600"></i>
                            Personal Identification
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="last_name-display">{{ $graduate->last_name ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <input type="text" name="last_name" value="{{ $graduate->last_name ?? '' }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="Enter last name">
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="first_name-display">{{ $graduate->first_name ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <input type="text" name="first_name" value="{{ $graduate->first_name ?? '' }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="Enter first name">
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="middle_name-display">{{ $graduate->middle_name ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <input type="text" name="middle_name" value="{{ $graduate->middle_name ?? '' }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="Enter middle name">
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">M.I.</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="middle_initial-display">{{ $graduate->middle_initial ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <input type="text" name="middle_initial" value="{{ $graduate->middle_initial ?? '' }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="Enter middle initial" maxlength="10">
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ext.</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="extension-display">{{ $graduate->extension ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <input type="text" name="extension" value="{{ $graduate->extension ?? '' }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="e.g., Jr., Sr., III" maxlength="10">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Demographic Details Section -->
                    <div class="bg-white p-6 rounded-lg border border-gray-200">
                        <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-users mr-2 text-blue-600"></i>
                            Demographic Details
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="gender-display">{{ $graduate->gender ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <select name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="">Select Gender</option>
                                        <option value="Male" {{ $graduate->gender === 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ $graduate->gender === 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ $graduate->gender === 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date of birth / Age</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="birth_date-display">
                                        @if($graduate->birth_date)
                                            {{ \Carbon\Carbon::parse($graduate->birth_date)->format('m/d/Y') }}
                                            @if($graduate->age) ({{ $graduate->age }} years old)@endif
                                        @else
                                            Not provided
                                        @endif
                                    </p>
                                </div>
                                <div class="field-edit hidden">
                                    <input type="date" name="birth_date" value="{{ $graduate->birth_date ?? '' }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Place of birth</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="place_of_birth-display">{{ $graduate->place_of_birth ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <input type="text" name="place_of_birth" value="{{ $graduate->place_of_birth ?? '' }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="Enter place of birth">
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Civil Status</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="civil_status-display">{{ $graduate->civil_status ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <select name="civil_status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="">Select Civil Status</option>
                                        <option value="Single" {{ $graduate->civil_status === 'Single' ? 'selected' : '' }}>Single</option>
                                        <option value="Married" {{ $graduate->civil_status === 'Married' ? 'selected' : '' }}>Married</option>
                                        <option value="Widowed" {{ $graduate->civil_status === 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                        <option value="Divorced" {{ $graduate->civil_status === 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                        <option value="Separated" {{ $graduate->civil_status === 'Separated' ? 'selected' : '' }}>Separated</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact and Background Section -->
                    <div class="bg-white p-6 rounded-lg border border-gray-200">
                        <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-address-book mr-2 text-blue-600"></i>
                            Contact and Background
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nationality</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="nationality-display">{{ $graduate->nationality ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <select name="nationality" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="">Select Nationality</option>
                                        <option value="Filipino" {{ $graduate->nationality === 'Filipino' ? 'selected' : '' }}>Filipino</option>
                                        <option value="American" {{ $graduate->nationality === 'American' ? 'selected' : '' }}>American</option>
                                        <option value="Chinese" {{ $graduate->nationality === 'Chinese' ? 'selected' : '' }}>Chinese</option>
                                        <option value="Japanese" {{ $graduate->nationality === 'Japanese' ? 'selected' : '' }}>Japanese</option>
                                        <option value="Korean" {{ $graduate->nationality === 'Korean' ? 'selected' : '' }}>Korean</option>
                                        <option value="Other" {{ $graduate->nationality === 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Religion</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="religion-display">{{ $graduate->religion ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <select name="religion" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="">Select Religion</option>
                                        <option value="Catholic" {{ $graduate->religion === 'Catholic' ? 'selected' : '' }}>Catholic</option>
                                        <option value="Protestant" {{ $graduate->religion === 'Protestant' ? 'selected' : '' }}>Protestant</option>
                                        <option value="Islam" {{ $graduate->religion === 'Islam' ? 'selected' : '' }}>Islam</option>
                                        <option value="Buddhism" {{ $graduate->religion === 'Buddhism' ? 'selected' : '' }}>Buddhism</option>
                                        <option value="Hinduism" {{ $graduate->religion === 'Hinduism' ? 'selected' : '' }}>Hinduism</option>
                                        <option value="Atheist" {{ $graduate->religion === 'Atheist' ? 'selected' : '' }}>Atheist</option>
                                        <option value="Other" {{ $graduate->religion === 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->email }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <input type="email" name="email" value="{{ auth()->user()->email }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contact #</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="contact_number-display">{{ $graduate->contact_number ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <input type="tel" name="contact_number" value="{{ $graduate->contact_number ?? '' }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="e.g., 09123456789">
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Height(m)</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="height-display">{{ $graduate->height ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <input type="text" name="height" value="{{ $graduate->height ?? '' }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="e.g., 1.75">
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Weight(kg)</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="weight-display">{{ $graduate->weight ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <input type="text" name="weight" value="{{ $graduate->weight ?? '' }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="e.g., 70">
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Blood Type</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="blood_type-display">{{ $graduate->blood_type ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <select name="blood_type" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
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
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ethnic Affiliation</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="ethnic_affiliation-display">{{ $graduate->ethnic_affiliation ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <select name="ethnic_affiliation" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="">Select Ethnicity</option>
                                        <option value="Cebuano" {{ $graduate->ethnic_affiliation === 'Cebuano' ? 'selected' : '' }}>Cebuano</option>
                                        <option value="Tagalog" {{ $graduate->ethnic_affiliation === 'Tagalog' ? 'selected' : '' }}>Tagalog</option>
                                        <option value="Ilocano" {{ $graduate->ethnic_affiliation === 'Ilocano' ? 'selected' : '' }}>Ilocano</option>
                                        <option value="Hiligaynon" {{ $graduate->ethnic_affiliation === 'Hiligaynon' ? 'selected' : '' }}>Hiligaynon</option>
                                        <option value="Waray" {{ $graduate->ethnic_affiliation === 'Waray' ? 'selected' : '' }}>Waray</option>
                                        <option value="Kapampangan" {{ $graduate->ethnic_affiliation === 'Kapampangan' ? 'selected' : '' }}>Kapampangan</option>
                                        <option value="Other" {{ $graduate->ethnic_affiliation === 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Health and Special Needs Section -->
                    <div class="bg-white p-6 rounded-lg border border-gray-200">
                        <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-heartbeat mr-2 text-blue-600"></i>
                            Health and Special Needs
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Vaccination Status</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="vaccination_status-display">{{ $graduate->vaccination_status ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <select name="vaccination_status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="">Select Vaccination Status</option>
                                        <option value="First Dose" {{ $graduate->vaccination_status === 'First Dose' ? 'selected' : '' }}>First Dose</option>
                                        <option value="Second Dose" {{ $graduate->vaccination_status === 'Second Dose' ? 'selected' : '' }}>Second Dose</option>
                                        <option value="Booster" {{ $graduate->vaccination_status === 'Booster' ? 'selected' : '' }}>Booster</option>
                                        <option value="Fully Vaccinated" {{ $graduate->vaccination_status === 'Fully Vaccinated' ? 'selected' : '' }}>Fully Vaccinated</option>
                                        <option value="Not Vaccinated" {{ $graduate->vaccination_status === 'Not Vaccinated' ? 'selected' : '' }}>Not Vaccinated</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">PWD-with special needs</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="pwd_special_needs-display">{{ $graduate->pwd_special_needs ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <select name="pwd_special_needs" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="">Select Status</option>
                                        <option value="YES" {{ $graduate->pwd_special_needs === 'YES' ? 'selected' : '' }}>YES</option>
                                        <option value="NO" {{ $graduate->pwd_special_needs === 'NO' ? 'selected' : '' }}>NO</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Present Address Section -->
                    <div class="bg-white p-6 rounded-lg border border-gray-200">
                        <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>
                            Present Address
                        </h4>
                        <div class="space-y-4">
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Address (House #/Block/Street/Subdivision/Building)</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="present_address-display">{{ $graduate->present_address ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <input type="text" name="present_address" value="{{ $graduate->present_address ?? '' }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="Enter complete address">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div class="field-group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Province / Region</label>
                                    <div class="field-display">
                                        <p class="mt-1 text-sm text-gray-900" id="province_region-display">{{ $graduate->province_region ?? 'Not provided' }}</p>
                                    </div>
                                    <div class="field-edit hidden">
                                        <input type="text" name="province_region" value="{{ $graduate->province_region ?? '' }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                               placeholder="e.g., Misamis Oriental">
                                    </div>
                                </div>
                                
                                <div class="field-group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Municipality / City</label>
                                    <div class="field-display">
                                        <p class="mt-1 text-sm text-gray-900" id="municipality_city-display">{{ $graduate->municipality_city ?? 'Not provided' }}</p>
                                    </div>
                                    <div class="field-edit hidden">
                                        <input type="text" name="municipality_city" value="{{ $graduate->municipality_city ?? '' }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                               placeholder="e.g., Cagayan de Oro City">
                                    </div>
                                </div>
                                
                                <div class="field-group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Barangay</label>
                                    <div class="field-display">
                                        <p class="mt-1 text-sm text-gray-900" id="barangay-display">{{ $graduate->barangay ?? 'Not provided' }}</p>
                                    </div>
                                    <div class="field-edit hidden">
                                        <input type="text" name="barangay" value="{{ $graduate->barangay ?? '' }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                               placeholder="e.g., Indahag">
                                    </div>
                                </div>
                                
                                <div class="field-group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Zip Code</label>
                                    <div class="field-display">
                                        <p class="mt-1 text-sm text-gray-900" id="zip_code-display">{{ $graduate->zip_code ?? 'Not provided' }}</p>
                                    </div>
                                    <div class="field-edit hidden">
                                        <input type="text" name="zip_code" value="{{ $graduate->zip_code ?? '' }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                               placeholder="e.g., 9000">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Permanent Address Section -->
                    <div class="bg-white p-6 rounded-lg border border-gray-200">
                        <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-home mr-2 text-blue-600"></i>
                            Permanent Address/Home Address
                        </h4>
                        <div class="space-y-4">
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Address (House #/Block/Street/Subdivision/Building)</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="permanent_address-display">{{ $graduate->permanent_address ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <input type="text" name="permanent_address" value="{{ $graduate->permanent_address ?? '' }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="Enter complete permanent address">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div class="field-group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Province / Region</label>
                                    <div class="field-display">
                                        <p class="mt-1 text-sm text-gray-900" id="permanent_province-display">{{ $graduate->permanent_province ?? 'Not provided' }}</p>
                                    </div>
                                    <div class="field-edit hidden">
                                        <input type="text" name="permanent_province" value="{{ $graduate->permanent_province ?? '' }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                               placeholder="e.g., Misamis Oriental">
                                    </div>
                                </div>
                                
                                <div class="field-group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Municipality / City</label>
                                    <div class="field-display">
                                        <p class="mt-1 text-sm text-gray-900" id="permanent_city-display">{{ $graduate->permanent_city ?? 'Not provided' }}</p>
                                    </div>
                                    <div class="field-edit hidden">
                                        <input type="text" name="permanent_city" value="{{ $graduate->permanent_city ?? '' }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                               placeholder="e.g., Cagayan de Oro City">
                                    </div>
                                </div>
                                
                                <div class="field-group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Barangay</label>
                                    <div class="field-display">
                                        <p class="mt-1 text-sm text-gray-900" id="permanent_barangay-display">{{ $graduate->permanent_barangay ?? 'Not provided' }}</p>
                                    </div>
                                    <div class="field-edit hidden">
                                        <input type="text" name="permanent_barangay" value="{{ $graduate->barangay ?? '' }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                               placeholder="e.g., Indahag">
                                    </div>
                                </div>
                                
                                <div class="field-group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Zip Code</label>
                                    <div class="field-display">
                                        <p class="mt-1 text-sm text-gray-900" id="permanent_zip_code-display">{{ $graduate->permanent_zip_code ?? 'Not provided' }}</p>
                                    </div>
                                    <div class="field-edit hidden">
                                        <input type="text" name="permanent_zip_code" value="{{ $graduate->permanent_zip_code ?? '' }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                               placeholder="e.g., 9000">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Information Section -->
                    <div class="bg-white p-6 rounded-lg border border-gray-200">
                        <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2 text-blue-600"></i>
                            Emergency Information
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Person</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="emergency_contact_person-display">{{ $graduate->emergency_contact_person ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <input type="text" name="emergency_contact_person" value="{{ $graduate->emergency_contact_person ?? '' }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="Enter emergency contact person name">
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Address</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="emergency_address-display">{{ $graduate->emergency_address ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <input type="text" name="emergency_address" value="{{ $graduate->emergency_address ?? '' }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="Enter emergency address">
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Mobile No</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="emergency_mobile-display">{{ $graduate->emergency_mobile ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <input type="tel" name="emergency_mobile" value="{{ $graduate->emergency_mobile ?? '' }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="e.g., 09123456789">
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Tel. No</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="emergency_telephone-display">{{ $graduate->emergency_telephone ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <input type="tel" name="emergency_telephone" value="{{ $graduate->emergency_telephone ?? '' }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="e.g., (088) 123-4567">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information Section -->
                    <div class="bg-white p-6 rounded-lg border border-gray-200">
                        <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-graduation-cap mr-2 text-blue-600"></i>
                            Academic Information
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Student ID</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="student_id-display">{{ $graduate->student_id ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <input type="text" name="student_id" value="{{ $graduate->student_id ?? '' }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="Enter student ID">
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Program</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="program-display">{{ $graduate->program ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <select name="program" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="">Select Program</option>
                                        <option value="Bachelor of Science in Information Technology" {{ $graduate->program === 'Bachelor of Science in Information Technology' ? 'selected' : '' }}>Bachelor of Science in Information Technology</option>
                                        <option value="Bachelor of Science in Computer Science" {{ $graduate->program === 'Bachelor of Science in Computer Science' ? 'selected' : '' }}>Bachelor of Science in Computer Science</option>
                                        <option value="Bachelor of Science in Information Systems" {{ $graduate->program === 'Bachelor of Science in Information Systems' ? 'selected' : '' }}>Bachelor of Science in Information Systems</option>
                                        <option value="Bachelor of Science in Computer Engineering" {{ $graduate->program === 'Bachelor of Science in Computer Engineering' ? 'selected' : '' }}>Bachelor of Science in Computer Engineering</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Batch Year</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="batch_year-display">{{ $graduate->batch_year ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <input type="number" name="batch_year" value="{{ $graduate->batch_year ?? '' }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="e.g., 2024" min="2000" max="2030">
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Graduation Date</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="graduation_date-display">{{ $graduate->graduation_date ? \Carbon\Carbon::parse($graduate->graduation_date)->format('M d, Y') : 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <input type="date" name="graduation_date" value="{{ $graduate->graduation_date ?? '' }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Employment Information Section -->
                    <div class="bg-white p-6 rounded-lg border border-gray-200">
                        <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-briefcase mr-2 text-blue-600"></i>
                            Employment Information
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Employment Status</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900">
                                        @if($graduate->is_employed)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Employed
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>
                                                Not Employed
                                            </span>
                                        @endif
                                    </p>
                                </div>
                                <div class="field-edit hidden">
                                    <select name="is_employed" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="0" {{ !$graduate->is_employed ? 'selected' : '' }}>Not Employed</option>
                                        <option value="1" {{ $graduate->is_employed ? 'selected' : '' }}>Employed</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Position</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="current_position-display">{{ $graduate->current_position ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <input type="text" name="current_position" value="{{ $graduate->current_position ?? '' }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="e.g., Software Developer">
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Company</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="current_company-display">{{ $graduate->current_company ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <input type="text" name="current_company" value="{{ $graduate->current_company ?? '' }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="e.g., Tech Company Inc.">
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Employment Start Date</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="employment_start_date-display">{{ $graduate->employment_start_date ? \Carbon\Carbon::parse($graduate->employment_start_date)->format('M d, Y') : 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <input type="date" name="employment_start_date" value="{{ $graduate->employment_start_date ?? '' }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Professional Information Section -->
                    <div class="bg-white p-6 rounded-lg border border-gray-200">
                        <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-user-tie mr-2 text-blue-600"></i>
                            Professional Information
                        </h4>
                        <div class="space-y-4">
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">LinkedIn Profile</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="linkedin_profile-display">
                                        @if($graduate->linkedin_profile)
                                            <a href="{{ $graduate->linkedin_profile }}" target="_blank" class="text-blue-600 hover:text-blue-800">{{ $graduate->linkedin_profile }}</a>
                                        @else
                                            Not provided
                                        @endif
                                    </p>
                                </div>
                                <div class="field-edit hidden">
                                    <input type="url" name="linkedin_profile" value="{{ $graduate->linkedin_profile ?? '' }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="https://linkedin.com/in/yourprofile">
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">About Me / Bio</label>
                                <div class="field-display">
                                    <p class="mt-1 text-sm text-gray-900" id="bio-display">{{ $graduate->bio ?? 'Not provided' }}</p>
                                </div>
                                <div class="field-edit hidden">
                                    <textarea name="bio" rows="4" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                              placeholder="Tell us about yourself, your interests, and career goals...">{{ $graduate->bio ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Save/Cancel Buttons -->
                    <div id="save-cancel-buttons" class="hidden flex justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="cancelEdit()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-save mr-2"></i>
                            Save Changes
                        </button>
                    </div>
                </form>
                
                <div class="mt-6">
                    <a href="{{ route('graduate.student-info') }}" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Complete Information
                    </a>
                </div>
            </div>

            <!-- Change Password Section -->
            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Change Password</h3>
                <form method="POST" action="{{ route('graduate.profile.change-password') }}" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2"></i>Current Password
                        </label>
                        <input type="password" id="current_password" name="current_password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('current_password') border-red-500 @enderror"
                               placeholder="Enter your current password">
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-key mr-2"></i>New Password
                        </label>
                        <input type="password" id="new_password" name="new_password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('new_password') border-red-500 @enderror"
                               placeholder="Enter your new password (min. 6 characters)">
                        @error('new_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-check-circle mr-2"></i>Confirm New Password
                        </label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Confirm your new password">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-save mr-2"></i>
                            Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Inline editing functionality
let isEditMode = false;
let originalValues = {};

function toggleEditMode() {
    isEditMode = !isEditMode;
    const editBtn = document.getElementById('edit-toggle-btn');
    const saveCancelBtns = document.getElementById('save-cancel-buttons');
    const fieldDisplays = document.querySelectorAll('.field-display');
    const fieldEdits = document.querySelectorAll('.field-edit');
    
    if (isEditMode) {
        // Enter edit mode
        editBtn.innerHTML = '<i class="fas fa-eye mr-2"></i>View Mode';
        editBtn.className = editBtn.className.replace('bg-blue-600 hover:bg-blue-700', 'bg-gray-600 hover:bg-gray-700');
        
        // Store original values
        originalValues = {};
        fieldEdits.forEach(editDiv => {
            const input = editDiv.querySelector('input, select');
            if (input) {
                originalValues[input.name] = input.value;
            }
        });
        
        // Show edit fields, hide display fields
        fieldDisplays.forEach(display => display.classList.add('hidden'));
        fieldEdits.forEach(edit => edit.classList.remove('hidden'));
        saveCancelBtns.classList.remove('hidden');
    } else {
        // Exit edit mode
        editBtn.innerHTML = '<i class="fas fa-edit mr-2"></i>Edit Information';
        editBtn.className = editBtn.className.replace('bg-gray-600 hover:bg-gray-700', 'bg-blue-600 hover:bg-blue-700');
        
        // Show display fields, hide edit fields
        fieldDisplays.forEach(display => display.classList.remove('hidden'));
        fieldEdits.forEach(edit => edit.classList.add('hidden'));
        saveCancelBtns.classList.add('hidden');
    }
}

function cancelEdit() {
    // Restore original values
    Object.keys(originalValues).forEach(fieldName => {
        const input = document.querySelector(`[name="${fieldName}"]`);
        if (input) {
            input.value = originalValues[fieldName];
        }
    });
    
    // Exit edit mode
    toggleEditMode();
}

// Handle form submission
document.getElementById('quick-info-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update display values
            updateDisplayValues(formData);
            
            // Show success message
            showNotification('Information updated successfully!', 'success');
            
            // Exit edit mode
            toggleEditMode();
        } else {
            console.error('Server error:', data);
            showNotification(data.message || 'Error updating information. Please try again.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating information. Please try again.', 'error');
    });
});

function updateDisplayValues(formData) {
    // Update all field displays
    const fields = [
        'last_name', 'first_name', 'middle_name', 'middle_initial', 'extension',
        'gender', 'place_of_birth', 'civil_status', 'nationality', 'religion',
        'contact_number', 'height', 'weight', 'blood_type', 'ethnic_affiliation',
        'vaccination_status', 'pwd_special_needs', 'present_address', 'province_region',
        'municipality_city', 'barangay', 'zip_code', 'permanent_address', 'permanent_province',
        'permanent_city', 'permanent_barangay', 'permanent_zip_code', 'emergency_contact_person',
        'emergency_address', 'emergency_mobile', 'emergency_telephone', 'linkedin_profile', 'bio',
        'student_id', 'program', 'batch_year', 'graduation_date', 'current_position', 
        'current_company', 'employment_start_date'
    ];
    
    fields.forEach(field => {
        const display = document.getElementById(field + '-display');
        const value = formData.get(field);
        if (display) {
            if (value) {
                display.textContent = value;
            } else {
                display.textContent = 'Not provided';
            }
        }
    });
    
    // Update birth date display with age
    const birthDateDisplay = document.getElementById('birth_date-display');
    const birthDate = formData.get('birth_date');
    if (birthDateDisplay && birthDate) {
        const date = new Date(birthDate);
        const age = Math.floor((new Date() - date) / (365.25 * 24 * 60 * 60 * 1000));
        birthDateDisplay.textContent = date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: '2-digit', 
            day: '2-digit' 
        }) + ` (${age} years old)`;
    }
    
    // Update graduation date display
    const graduationDateDisplay = document.getElementById('graduation_date-display');
    const graduationDate = formData.get('graduation_date');
    if (graduationDateDisplay && graduationDate) {
        const date = new Date(graduationDate);
        graduationDateDisplay.textContent = date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    }
    
    // Update employment start date display
    const employmentStartDateDisplay = document.getElementById('employment_start_date-display');
    const employmentStartDate = formData.get('employment_start_date');
    if (employmentStartDateDisplay && employmentStartDate) {
        const date = new Date(employmentStartDate);
        employmentStartDateDisplay.textContent = date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    }
    
    // Update LinkedIn profile display with link
    const linkedinDisplay = document.getElementById('linkedin_profile-display');
    const linkedinUrl = formData.get('linkedin_profile');
    if (linkedinDisplay && linkedinUrl) {
        linkedinDisplay.innerHTML = `<a href="${linkedinUrl}" target="_blank" class="text-blue-600 hover:text-blue-800">${linkedinUrl}</a>`;
    }
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function uploadProfilePicture(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file type
        if (!file.type.startsWith('image/')) {
            alert('Please select an image file.');
            return;
        }
        
        // Validate file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB.');
            return;
        }
        
        const formData = new FormData();
        formData.append('profile_picture', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Show loading state
        const button = input.previousElementSibling;
        const originalText = button.textContent;
        button.textContent = 'Uploading...';
        button.disabled = true;
        
        fetch('{{ route("graduate.profile.picture") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the profile picture display in sidebar
                const profileImg = document.querySelector('.w-24.h-24 img');
                const profileIcon = document.querySelector('.w-24.h-24 i');
                
                if (profileImg) {
                    profileImg.src = data.profile_picture_url + '?t=' + new Date().getTime();
                } else if (profileIcon) {
                    profileIcon.parentElement.innerHTML = `<img src="${data.profile_picture_url}?t=${new Date().getTime()}" alt="Profile Picture" class="w-full h-full object-cover">`;
                }
                
                // Update the profile picture preview in the main section
                const profilePreview = document.getElementById('profile-picture-preview');
                if (profilePreview) {
                    profilePreview.src = data.profile_picture_url + '?t=' + new Date().getTime();
                } else {
                    // If no preview exists, find the preview container and add the image
                    const previewContainer = document.querySelector('.bg-yellow-400.rounded-full');
                    if (previewContainer) {
                        const existingIcon = previewContainer.querySelector('i');
                        if (existingIcon) {
                            existingIcon.style.display = 'none';
                            const newImg = document.createElement('img');
                            newImg.src = data.profile_picture_url + '?t=' + new Date().getTime();
                            newImg.alt = 'Profile Picture';
                            newImg.className = 'w-full h-full object-cover';
                            newImg.id = 'profile-picture-preview';
                            previewContainer.appendChild(newImg);
                        }
                    }
                }
                
                // Update the header profile picture
                const headerProfilePicture = document.getElementById('header-profile-picture');
                if (headerProfilePicture) {
                    const existingImg = headerProfilePicture.querySelector('img');
                    const existingIcon = headerProfilePicture.querySelector('i');
                    
                    if (existingImg) {
                        existingImg.src = data.profile_picture_url + '?t=' + new Date().getTime();
                    } else if (existingIcon) {
                        existingIcon.style.display = 'none';
                        const newImg = document.createElement('img');
                        newImg.src = data.profile_picture_url + '?t=' + new Date().getTime();
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
        })
        .finally(() => {
            button.textContent = originalText;
            button.disabled = false;
            input.value = '';
        });
    }
}
</script>
