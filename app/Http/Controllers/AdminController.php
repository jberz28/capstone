<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Graduate;
use App\Models\JobPosting;
use App\Models\AlumniActivity;
use App\Models\AlumniMembership;
use App\Models\Resume;
use App\Models\CareerReport;
use App\Models\EmploymentRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get statistics for admin dashboard
        $stats = [
            'total_users' => User::count(),
            'total_graduates' => Graduate::count(),
            'verified_graduates' => Graduate::where('verification_status', 'verified')->count(),
            'pending_verifications' => Graduate::where('verification_status', 'pending')->count(),
            'total_job_postings' => JobPosting::count(),
            'active_job_postings' => JobPosting::where('is_active', true)->count(),
            'pending_job_reviews' => JobPosting::where('status', 'pending')->count(),
            'employed_graduates' => Graduate::where('is_employed', true)->count(),
            'employment_rate' => Graduate::count() > 0 ? round((Graduate::where('is_employed', true)->count() / Graduate::count()) * 100, 1) : 0,
        ];

        // Get recent graduates
        $recent_graduates = Graduate::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent job postings
        $recent_job_postings = JobPosting::with('postedBy')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_graduates', 'recent_job_postings'));
    }

    public function users()
    {
        $users = User::with('graduate')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.users', compact('users'));
    }

    public function userDetails(User $user)
    {
        $user->load('graduate');
        
        $html = view('admin.partials.user-details', compact('user'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    public function verifyUser(Request $request, User $user)
    {
        \Log::info('Verification request received', [
            'user_id' => $user->id,
            'verification_status' => $request->verification_status,
            'verification_notes' => $request->verification_notes,
            'is_ajax' => $request->ajax()
        ]);

        $request->validate([
            'verification_status' => 'required|in:verified,rejected',
            'verification_notes' => 'nullable|string|max:500',
        ]);

        try {
            if ($user->graduate) {
                $user->graduate->update([
                    'verification_status' => $request->verification_status,
                    'verification_notes' => $request->verification_notes,
                ]);
            } else {
                // If user doesn't have a graduate record, create one
                $user->graduate()->create([
                    'user_id' => $user->id,
                    'student_id' => 'TEMP-' . $user->id,
                    'program' => 'To be updated',
                    'batch_year' => date('Y'),
                    'graduation_date' => now(),
                    'first_name' => $user->name,
                    'last_name' => '',
                    'verification_status' => $request->verification_status,
                    'verification_notes' => $request->verification_notes,
                ]);
            }

            \Log::info('Verification successful', [
                'user_id' => $user->id,
                'verification_status' => $request->verification_status
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User verification status updated successfully!'
                ]);
            }

            return redirect()->route('admin.users')->with('success', 'User verification status updated successfully!');
        } catch (\Exception $e) {
            \Log::error('User verification failed: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update verification status: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('admin.users')->with('error', 'Failed to update verification status.');
        }
    }

    public function dataMonitoring()
    {
        // Basic statistics
        $stats = [
            'total_users' => User::count(),
            'total_graduates' => Graduate::count(),
            'total_jobs' => JobPosting::count(),
            'total_memberships' => AlumniMembership::count(),
            'employed_graduates' => Graduate::where('is_employed', true)->count(),
            'unemployed_graduates' => Graduate::where('is_employed', false)->count(),
            'employment_rate' => Graduate::count() > 0 ? round((Graduate::where('is_employed', true)->count() / Graduate::count()) * 100, 1) : 0,
            'total_tables' => 15, // Approximate number of tables
            'db_size' => 'N/A', // Would need database-specific queries
            'profile_pictures' => Graduate::whereNotNull('profile_picture')->count(),
            'payment_proofs' => AlumniMembership::whereNotNull('payment_proof')->count(),
            'resumes' => Resume::count(),
            'response_time' => rand(50, 200), // Mock response time
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'uptime' => '99.9%', // Mock uptime
        ];

        // Recent users
        $recent_users = User::orderBy('created_at', 'desc')->limit(5)->get();

        // Recent job postings
        $recent_jobs = JobPosting::orderBy('created_at', 'desc')->limit(5)->get();

        // Employment statistics by program
        $employment_by_program = Graduate::selectRaw('program, COUNT(*) as total, SUM(CASE WHEN is_employed = 1 THEN 1 ELSE 0 END) as employed')
            ->groupBy('program')
            ->get()
            ->map(function ($item) {
                $item->employment_rate = $item->total > 0 ? round(($item->employed / $item->total) * 100, 1) : 0;
                return $item;
            });

        // Employment statistics by batch year
        $employment_by_batch = Graduate::selectRaw('batch_year, COUNT(*) as total, SUM(CASE WHEN is_employed = 1 THEN 1 ELSE 0 END) as employed')
            ->groupBy('batch_year')
            ->orderBy('batch_year', 'desc')
            ->get()
            ->map(function ($item) {
                $item->employment_rate = $item->total > 0 ? round(($item->employed / $item->total) * 100, 1) : 0;
                return $item;
            });

        // Salary statistics
        $salary_stats = Graduate::where('is_employed', true)
            ->whereNotNull('salary')
            ->selectRaw('AVG(salary) as avg_salary, MIN(salary) as min_salary, MAX(salary) as max_salary')
            ->first();

        // Recent employment updates
        $recent_employment_updates = Graduate::with('user')
            ->where('is_employed', true)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.data-monitoring', compact(
            'stats',
            'recent_users',
            'recent_jobs',
            'employment_by_program',
            'employment_by_batch',
            'salary_stats',
            'recent_employment_updates'
        ));
    }

    public function jobPostings()
    {
        $jobPostings = JobPosting::with(['postedBy.graduate'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.job-postings', compact('jobPostings'));
    }

    public function createJobPosting()
    {
        return view('admin.job-postings.create');
    }

    public function storeJobPosting(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'company' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'employment_type' => 'required|string|in:full-time,part-time,contract,internship,remote',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'application_deadline' => 'nullable|date|after:today',
        ]);

        JobPosting::create([
            'posted_by' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'company' => $request->company,
            'location' => $request->location,
            'employment_type' => $request->employment_type,
            'salary_min' => $request->salary_min,
            'salary_max' => $request->salary_max,
            'requirements' => $request->requirements,
            'benefits' => $request->benefits,
            'application_deadline' => $request->application_deadline,
            'status' => 'approved', // Admin jobs are approved immediately
            'is_active' => true,
        ]);

        return redirect()->route('admin.job-postings')->with('success', 'Job posting created successfully!');
    }

    public function jobDetails(JobPosting $jobPosting)
    {
        $jobPosting->load('postedBy.graduate');
        
        $html = view('admin.partials.job-details', compact('jobPosting'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    public function deleteJob(JobPosting $jobPosting)
    {
        try {
            $jobPosting->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Job posting deleted successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting job posting:', [
                'error' => $e->getMessage(),
                'job_id' => $jobPosting->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting job posting. Please try again.'
            ], 500);
        }
    }

    public function updateJobStatus(Request $request, JobPosting $jobPosting)
    {
        $request->validate([
            'status' => 'required|in:draft,published,closed',
        ]);

        $jobPosting->update([
            'status' => $request->status,
            'is_active' => $request->status === 'published',
        ]);

        return redirect()->route('admin.job-postings')->with('success', 'Job posting status updated successfully!');
    }

    public function reports()
    {
        $reports = CareerReport::with('generatedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.reports', compact('reports'));
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:employment_summary,program_analysis,salary_analysis,career_trends',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $data = $this->generateReportData($request->report_type);

        CareerReport::create([
            'title' => $request->title,
            'report_type' => $request->report_type,
            'description' => $request->description,
            'data' => $data,
            'generated_by' => auth()->id(),
            'generated_at' => now(),
        ]);

        return redirect()->route('admin.reports')->with('success', 'Report generated successfully!');
    }

    public function maintenance()
    {
        // Get system statistics
        $system_stats = [
            'total_users' => User::count(),
            'total_graduates' => Graduate::count(),
            'total_job_postings' => JobPosting::count(),
            'total_employment_records' => EmploymentRecord::count(),
            'total_reports' => CareerReport::count(),
        ];

        return view('admin.maintenance', compact('system_stats'));
    }

    public function backup(Request $request)
    {
        // In a real application, you would implement actual backup functionality
        // For now, we'll just return a success message
        
        $request->validate([
            'backup_type' => 'required|in:full,database,files',
        ]);

        // Simulate backup process
        sleep(2); // Simulate processing time

        return redirect()->route('admin.maintenance')->with('success', 'Backup completed successfully!');
    }

    private function generateReportData($reportType)
    {
        switch ($reportType) {
            case 'employment_summary':
                return [
                    'total_graduates' => Graduate::count(),
                    'employed_graduates' => Graduate::where('is_employed', true)->count(),
                    'unemployed_graduates' => Graduate::where('is_employed', false)->count(),
                    'employment_rate' => Graduate::count() > 0 ? round((Graduate::where('is_employed', true)->count() / Graduate::count()) * 100, 1) : 0,
                    'verified_graduates' => Graduate::where('verification_status', 'verified')->count(),
                    'pending_verifications' => Graduate::where('verification_status', 'pending')->count(),
                ];

            case 'program_analysis':
                return Graduate::selectRaw('program, COUNT(*) as total, SUM(CASE WHEN is_employed = 1 THEN 1 ELSE 0 END) as employed')
                    ->groupBy('program')
                    ->get()
                    ->map(function ($item) {
                        $item->employment_rate = $item->total > 0 ? round(($item->employed / $item->total) * 100, 1) : 0;
                        return $item;
                    })
                    ->toArray();

            case 'salary_analysis':
                $employed_graduates = Graduate::where('is_employed', true)->whereNotNull('salary');
                return [
                    'average_salary' => $employed_graduates->avg('salary'),
                    'min_salary' => $employed_graduates->min('salary'),
                    'max_salary' => $employed_graduates->max('salary'),
                    'salary_by_program' => Graduate::selectRaw('program, AVG(salary) as avg_salary')
                        ->where('is_employed', true)
                        ->whereNotNull('salary')
                        ->groupBy('program')
                        ->get()
                        ->toArray(),
                ];

            case 'career_trends':
                return [
                    'employment_by_batch' => Graduate::selectRaw('batch_year, COUNT(*) as total, SUM(CASE WHEN is_employed = 1 THEN 1 ELSE 0 END) as employed')
                        ->groupBy('batch_year')
                        ->orderBy('batch_year', 'desc')
                        ->get()
                        ->map(function ($item) {
                            $item->employment_rate = $item->total > 0 ? round(($item->employed / $item->total) * 100, 1) : 0;
                            return $item;
                        })
                        ->toArray(),
                    'recent_employment_updates' => Graduate::where('is_employed', true)
                        ->orderBy('updated_at', 'desc')
                        ->limit(20)
                        ->get(['current_position', 'current_company', 'updated_at'])
                        ->toArray(),
                ];

            default:
                return [];
        }
    }

    public function checkNotifications()
    {
        $pendingCount = JobPosting::where('status', 'pending')->count();
        
        return response()->json([
            'hasNewNotifications' => $pendingCount > 0,
            'pendingCount' => $pendingCount
        ]);
    }

    public function createUser()
    {
        return view('admin.create-user');
    }

    public function storeUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,staff,graduate',
            'password' => 'nullable|string|min:6',
            'student_id' => 'required_if:role,graduate|string|max:255|unique:graduates,student_id',
            'program' => 'required_if:role,graduate|string|max:255',
            'batch_year' => 'required_if:role,graduate|integer|min:2000|max:' . (date('Y') + 10),
            'graduation_date' => 'nullable|date',
            'send_email' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $password = $request->password ?? 'password';
            
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($password),
                'role' => $request->role,
                'email_verified_at' => now(),
            ]);

            // If user is a graduate, create graduate record
            if ($request->role === 'graduate') {
                Graduate::create([
                    'user_id' => $user->id,
                    'student_id' => $request->student_id,
                    'program' => $request->program,
                    'batch_year' => $request->batch_year,
                    'graduation_date' => $request->graduation_date ?? now(),
                    'first_name' => explode(' ', $request->name)[0],
                    'last_name' => explode(' ', $request->name)[1] ?? '',
                    'verification_status' => 'verified',
                ]);
            }

            // Send email if requested
            $emailSent = false;
            if ($request->send_email) {
                $emailSent = $this->sendAccountCredentials($user, $password);
            }

            $message = 'User account created successfully!';
            if ($request->send_email) {
                if ($emailSent) {
                    $message .= ' Credentials sent via email.';
                } else {
                    $message .= ' Email failed to send. Please manually share these credentials:';
                    $message .= ' Email: ' . $user->email . ', Password: ' . $password;
                }
            }

            return redirect()->route('admin.users')->with('success', $message);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create user: ' . $e->getMessage())->withInput();
        }
    }

    private function sendAccountCredentials($user, $password)
    {
        try {
            Mail::send('emails.account-credentials', [
                'user' => $user,
                'password' => $password,
                'loginUrl' => route('login')
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Your USTP Graduate Tracking System Account');
            });
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send account credentials email: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteUser(User $user)
    {
        try {
            // Prevent admin from deleting themselves
            if ($user->id === auth()->id()) {
                return redirect()->route('admin.users')->with('error', 'You cannot delete your own account.');
            }

            // Delete associated graduate record if exists
            if ($user->graduate) {
                $user->graduate->delete();
            }

            // Delete the user
            $user->delete();

            return redirect()->route('admin.users')->with('success', 'User account deleted successfully.');
            
        } catch (\Exception $e) {
            return redirect()->route('admin.users')->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    public function alumniActivities()
    {
        $activities = AlumniActivity::orderBy('event_date', 'desc')->paginate(15);
        
        $stats = [
            'total' => AlumniActivity::count(),
            'published' => AlumniActivity::where('status', 'published')->count(),
            'draft' => AlumniActivity::where('status', 'draft')->count(),
            'upcoming' => AlumniActivity::published()->upcoming()->count(),
            'featured' => AlumniActivity::featured()->count(),
        ];

        return view('admin.alumni-activities', compact('activities', 'stats'));
    }

    public function createAlumniActivity()
    {
        return view('admin.alumni-activities.create');
    }

    public function storeAlumniActivity(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:homecoming,reunion,mentorship,networking,workshop,other',
            'batch_year' => 'nullable|string|max:4',
            'event_date' => 'required|date|after:today',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'venue' => 'nullable|string|max:255',
            'registration_fee' => 'nullable|numeric|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'status' => 'required|in:draft,published,cancelled,completed',
            'is_featured' => 'boolean',
            'registration_deadline' => 'nullable|date|after:today|before:event_date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('alumni-activities', 'public');
            $validated['image'] = $imagePath;
        }

        AlumniActivity::create($validated);

        return redirect()->route('admin.alumni-activities')
            ->with('success', 'Alumni activity created successfully.');
    }

    public function editAlumniActivity(AlumniActivity $alumniActivity)
    {
        return view('admin.alumni-activities.edit', compact('alumniActivity'));
    }

    public function updateAlumniActivity(Request $request, AlumniActivity $alumniActivity)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:homecoming,reunion,mentorship,networking,workshop,other',
            'batch_year' => 'nullable|string|max:4',
            'event_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'venue' => 'nullable|string|max:255',
            'registration_fee' => 'nullable|numeric|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'status' => 'required|in:draft,published,cancelled,completed',
            'is_featured' => 'boolean',
            'registration_deadline' => 'nullable|date|before:event_date',
        ]);

        $alumniActivity->update($validated);

        return redirect()->route('admin.alumni-activities')
            ->with('success', 'Alumni activity updated successfully.');
    }

    public function deleteAlumniActivity(AlumniActivity $alumniActivity)
    {
        $alumniActivity->delete();

        return redirect()->route('admin.alumni-activities')
            ->with('success', 'Alumni activity deleted successfully.');
    }

    public function updateAlumniActivityStatus(Request $request, AlumniActivity $alumniActivity)
    {
        $request->validate([
            'status' => 'required|in:draft,published,cancelled,completed',
        ]);

        $alumniActivity->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Activity status updated successfully!',
            'status' => $alumniActivity->status
        ]);
    }

    public function alumniMemberships()
    {
        $memberships = AlumniMembership::with(['graduate.user', 'verifier'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $stats = [
            'total' => AlumniMembership::count(),
            'pending' => AlumniMembership::pending()->count(),
            'paid' => AlumniMembership::paid()->count(),
            'verified' => AlumniMembership::verified()->count(),
            'expired' => AlumniMembership::expired()->count(),
        ];

        return view('admin.alumni-memberships', compact('memberships', 'stats'));
    }

    public function verifyMembership(Request $request, AlumniMembership $membership)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $membership->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => auth()->id(),
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Membership verified successfully!',
            'status' => $membership->status
        ]);
    }

    public function confirmPayment(AlumniMembership $membership)
    {
        $membership->update([
            'status' => 'paid',
            'payment_date' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment confirmed successfully!',
            'status' => $membership->status
        ]);
    }

    public function rejectMembership(Request $request, AlumniMembership $membership)
    {
        $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        $membership->update([
            'status' => 'cancelled',
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Membership rejected successfully!',
            'status' => $membership->status
        ]);
    }

    public function deliverMembership(AlumniMembership $membership)
    {
        // Only allow delivery for Cash on Delivery memberships
        if ($membership->payment_method !== 'cash_on_delivery') {
            return response()->json([
                'success' => false,
                'message' => 'This action is only available for Cash on Delivery memberships.'
            ], 400);
        }

        $membership->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => auth()->id(),
            'payment_date' => now(), // Mark payment as received upon delivery
            'notes' => 'Delivered and payment received via Cash on Delivery',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Membership delivered and verified successfully!',
            'status' => $membership->status
        ]);
    }

    public function deleteMembership(AlumniMembership $membership)
    {
        $membership->delete();

        return redirect()->route('admin.alumni-memberships')
            ->with('success', 'Membership deleted successfully.');
    }

    public function membershipDetails(AlumniMembership $membership)
    {
        try {
            // Load the membership with all related data
            $membership->load(['graduate.user', 'verifier']);
            
            // Append accessor attributes to the JSON response
            $membershipArray = $membership->toArray();
            $membershipArray['membership_type_label'] = $membership->membership_type_label;
            $membershipArray['status_label'] = $membership->status_label;
            $membershipArray['payment_method_label'] = $membership->payment_method_label;
            $membershipArray['formatted_amount'] = $membership->formatted_amount;
            $membershipArray['formatted_membership_period'] = $membership->formatted_membership_period;
            $membershipArray['days_remaining'] = $membership->days_remaining;
            
            return response()->json([
                'success' => true,
                'membership' => $membershipArray
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching membership details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading membership details'
            ], 500);
        }
    }

    public function graduationApplications()
    {
        $applications = \App\Models\GraduationApplication::with(['graduate.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total' => \App\Models\GraduationApplication::count(),
            'pending' => \App\Models\GraduationApplication::where('status', 'pending')->count(),
            'approved' => \App\Models\GraduationApplication::where('status', 'approved')->count(),
            'rejected' => \App\Models\GraduationApplication::where('status', 'rejected')->count(),
        ];

        return view('admin.graduation-applications', compact('applications', 'stats'));
    }

    public function showGraduationApplication(\App\Models\GraduationApplication $application)
    {
        $application->load(['graduate.user', 'approver']);
        return view('admin.graduation-application-details', compact('application'));
    }

    public function approveGraduationApplication(Request $request, \App\Models\GraduationApplication $application)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $application->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.graduation-applications')
            ->with('success', 'Graduation application approved successfully.');
    }

    public function rejectGraduationApplication(Request $request, \App\Models\GraduationApplication $application)
    {
        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $application->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.graduation-applications')
            ->with('success', 'Graduation application rejected.');
    }
}
