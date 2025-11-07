@extends('layouts.dashboard')

@section('content')
<div class="p-6">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Job Postings Management</h1>
                <p class="text-gray-600 mt-2">Manage and monitor all job postings in the system</p>
            </div>
            <div>
                <a href="{{ route('admin.job-postings.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Post New Job
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-briefcase text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Jobs</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $jobPostings->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Jobs</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $jobPostings->where('status', 'active')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending Review</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $jobPostings->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Inactive Jobs</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $jobPostings->where('status', 'inactive')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center space-x-2">
                <label for="status-filter" class="text-sm font-medium text-gray-700">Status:</label>
                <select id="status-filter" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            
            <div class="flex items-center space-x-2">
                <label for="search" class="text-sm font-medium text-gray-700">Search:</label>
                <input type="text" id="search" placeholder="Search by title, company..." 
                       class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <button onclick="applyFilters()" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 transition-colors">
                <i class="fas fa-search mr-1"></i>Filter
            </button>
            
            <button onclick="clearFilters()" class="bg-gray-600 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-700 transition-colors">
                <i class="fas fa-times mr-1"></i>Clear
            </button>
        </div>
    </div>

    <!-- Job Postings Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">All Job Postings</h2>
        </div>

        @if($jobPostings->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posted By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applications</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posted Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($jobPostings as $job)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $job->title }}</div>
                            <div class="text-sm text-gray-500">{{ $job->location }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $job->company_name }}</div>
                            <div class="text-sm text-gray-500">{{ $job->employment_type }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8">
                                    @if($job->postedBy && $job->postedBy->graduate && $job->postedBy->graduate->profile_picture)
                                        <img class="h-8 w-8 rounded-full object-cover" src="{{ asset('storage/' . $job->postedBy->graduate->profile_picture) }}" alt="">
                                    @else
                                        <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                            <i class="fas fa-user text-gray-600 text-xs"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $job->postedBy ? ($job->postedBy->graduate ? $job->postedBy->graduate->first_name . ' ' . $job->postedBy->graduate->last_name : $job->postedBy->name) : 'System' }}
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $job->postedBy ? $job->postedBy->email : 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($job->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    Pending
                                    <span class="ml-2 inline-block w-2 h-2 bg-red-500 rounded-full"></span>
                                </span>
                            @elseif($job->status === 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-users mr-1"></i>
                                {{ $job->applications_count ?? 0 }} applications
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $job->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button onclick="viewJob({{ $job->id }})" 
                                        class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md text-xs transition-colors">
                                    <i class="fas fa-eye mr-1"></i>View
                                </button>
                                
                                @if($job->status === 'pending')
                                <button onclick="rejectJob({{ $job->id }})" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md text-xs transition-colors">
                                    <i class="fas fa-times mr-1"></i>Reject
                                </button>
                                <button onclick="approveJob({{ $job->id }})" class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-md text-xs transition-colors">
                                    <i class="fas fa-check mr-1"></i>Approve
                                </button>
                                @endif
                                
                                @if($job->status === 'active')
                                <button onclick="updateJobStatus({{ $job->id }}, 'inactive')" 
                                        class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md text-xs transition-colors">
                                    <i class="fas fa-pause mr-1"></i>Deactivate
                                </button>
                                @elseif($job->status === 'inactive')
                                <button onclick="updateJobStatus({{ $job->id }}, 'active')" 
                                        class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-md text-xs transition-colors">
                                    <i class="fas fa-play mr-1"></i>Activate
                                </button>
                                @endif
                                
                                <button onclick="deleteJob({{ $job->id }})" 
                                        class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md text-xs transition-colors">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $jobPostings->links() }}
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <i class="fas fa-briefcase text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No job postings found</h3>
            <p class="text-gray-500">There are no job postings in the system yet.</p>
        </div>
        @endif
    </div>
</div>

<!-- Job Details Modal -->
<div id="jobModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Job Posting Details</h3>
                <button onclick="closeJobModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="jobDetails" class="text-gray-600">
                <!-- Job details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Delete Job Confirmation Modal -->
<div id="delete-job-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
            <div class="p-8">
                <!-- Warning Icon -->
                <div class="flex justify-center mb-6">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Title and Message -->
                <div class="text-center mb-8">
                    <h3 class="text-xl font-display font-bold text-gray-900 mb-3">Delete Job Posting</h3>
                    <p class="text-gray-600 font-body leading-relaxed">
                        Are you sure you want to delete this job posting? This action cannot be undone and will permanently remove:
                    </p>
                    <ul class="text-sm text-gray-500 mt-3 space-y-1">
                        <li>• Job posting details</li>
                        <li>• All applications (if any)</li>
                        <li>• Related data</li>
                    </ul>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex space-x-3">
                    <button onclick="closeDeleteJobModal()" 
                            class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition-colors font-display font-semibold">
                        Cancel
                    </button>
                    <button onclick="confirmDeleteJob()" 
                            class="flex-1 bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition-colors font-display font-semibold">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div id="rejection-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Reject Job Posting</h3>
                <form id="rejection-form">
                    <div class="mb-4">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for rejection:
                        </label>
                        <textarea id="rejection_reason" name="rejection_reason" rows="4" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Please provide a reason for rejecting this job posting..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeRejectionModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Reject Job
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function viewJob(jobId) {
    // Show loading
    document.getElementById('jobDetails').innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i></div>';
    document.getElementById('jobModal').classList.remove('hidden');
    
    // Fetch job details
    fetch(`/admin/job-postings/${jobId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('jobDetails').innerHTML = data.html;
            } else {
                document.getElementById('jobDetails').innerHTML = '<div class="text-red-600">Error loading job details.</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('jobDetails').innerHTML = '<div class="text-red-600">Error loading job details.</div>';
        });
}

function closeJobModal() {
    document.getElementById('jobModal').classList.add('hidden');
}

function updateJobStatus(jobId, status) {
    const action = status === 'active' ? 'activate' : status === 'inactive' ? 'deactivate' : 'approve';
    const confirmMessage = `Are you sure you want to ${action} this job posting?`;
    
    if (confirm(confirmMessage)) {
        fetch(`/admin/job-postings/${jobId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating job status: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating job status. Please try again.');
        });
    }
}

let jobToDelete = null;

function deleteJob(jobId) {
    jobToDelete = jobId;
    document.getElementById('delete-job-modal').classList.remove('hidden');
}

function closeDeleteJobModal() {
    jobToDelete = null;
    document.getElementById('delete-job-modal').classList.add('hidden');
}

function confirmDeleteJob() {
    if (jobToDelete) {
        fetch(`/admin/job-postings/${jobToDelete}/delete`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting job posting: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting job posting. Please try again.');
        });
    }
}

function applyFilters() {
    const status = document.getElementById('status-filter').value;
    const search = document.getElementById('search').value;
    
    let url = new URL(window.location);
    if (status) url.searchParams.set('status', status);
    if (search) url.searchParams.set('search', search);
    
    window.location.href = url.toString();
}

function clearFilters() {
    document.getElementById('status-filter').value = '';
    document.getElementById('search').value = '';
    window.location.href = window.location.pathname;
}

let currentJobId = null;
function approveJob(jobId) {
    if (confirm('Are you sure you want to approve this job posting?')) {
        fetch(`/admin/jobs/${jobId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Job approved successfully!');
                window.location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to approve job'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error approving job. Please try again.');
        });
    }
}
function rejectJob(jobId) {
    currentJobId = jobId;
    document.getElementById('rejection-modal').classList.remove('hidden');
}
function closeRejectionModal() {
    document.getElementById('rejection-modal').classList.add('hidden');
    document.getElementById('rejection-form').reset();
    currentJobId = null;
}
document.getElementById('rejection-form').addEventListener('submit', function(e) {
    e.preventDefault();
    if (!currentJobId) return;
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    submitButton.textContent = 'Rejecting...';
    submitButton.disabled = true;
    fetch(`/admin/jobs/${currentJobId}/reject`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Job rejected successfully!');
            closeRejectionModal();
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to reject job'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error rejecting job. Please try again.');
    })
    .finally(() => {
        submitButton.textContent = originalText;
        submitButton.disabled = false;
    });
});
</script>
@endsection
