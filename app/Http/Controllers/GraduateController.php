<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Graduate;
use App\Models\JobPosting;
use App\Models\Resume;
use App\Models\EmploymentRecord;
use App\Models\AlumniActivity;
use App\Models\AlumniMembership;
use App\Events\NewJobPostingSubmitted;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class GraduateController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        $graduate = $user->graduate;
        
        // If no graduate record exists, create one with basic info
        if (!$graduate) {
            $graduate = Graduate::create([
                'user_id' => $user->id,
                'student_id' => 'TEMP-' . $user->id,
                'program' => 'To be updated',
                'batch_year' => date('Y'),
                'graduation_date' => now(),
                'first_name' => $user->name, // Use the user's name as default
                'last_name' => '',
            ]);
        }
        
        // Get alumni activities
        $alumniActivities = AlumniActivity::published()
            ->upcoming()
            ->orderBy('event_date', 'asc')
            ->limit(6)
            ->get();

        // Get batch-specific activities
        $batchActivities = AlumniActivity::published()
            ->upcoming()
            ->byBatch($graduate->batch_year)
            ->orderBy('event_date', 'asc')
            ->limit(3)
            ->get();

        if ($request->ajax()) {
            return view('graduate.partials.dashboard', compact('graduate', 'alumniActivities', 'batchActivities'));
        }
        
        return view('graduate.dashboard', compact('graduate', 'alumniActivities', 'batchActivities'));
    }

    public function alumniActivities(Request $request)
    {
        $user = auth()->user();
        $graduate = $user->graduate;
        
        // Get all published alumni activities
        $allActivities = AlumniActivity::published()
            ->upcoming()
            ->orderBy('event_date', 'asc')
            ->paginate(12);

        // Get batch-specific activities
        $batchActivities = AlumniActivity::published()
            ->upcoming()
            ->byBatch($graduate->batch_year ?? null)
            ->orderBy('event_date', 'asc')
            ->get();

        // Get featured activities
        $featuredActivities = AlumniActivity::published()
            ->upcoming()
            ->featured()
            ->orderBy('event_date', 'asc')
            ->limit(3)
            ->get();

        // Get activities by type
        $activityTypes = AlumniActivity::published()
            ->upcoming()
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get();

        if ($request->ajax()) {
            return view('graduate.partials.alumni-activities', compact('allActivities', 'batchActivities', 'featuredActivities', 'activityTypes', 'graduate'));
        }
        
        return view('graduate.alumni-activities', compact('allActivities', 'batchActivities', 'featuredActivities', 'activityTypes', 'graduate'));
    }

    public function alumniActivityDetails(AlumniActivity $alumniActivity)
    {
        // Ensure the activity is published and accessible to graduates
        if ($alumniActivity->status !== 'published') {
            return response()->json([
                'success' => false,
                'message' => 'Activity not found or not accessible.'
            ], 404);
        }

        $html = view('graduate.partials.alumni-activity-details', compact('alumniActivity'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    public function alumniMembership(Request $request)
    {
        $user = auth()->user();
        $graduate = $user->graduate;
        
        // Get current membership
        $currentMembership = $graduate->alumniMemberships()
            ->where('status', 'verified')
            ->where('membership_end_date', '>=', now()->toDateString())
            ->first();

        // Get membership history
        $membershipHistory = $graduate->alumniMemberships()
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all pending memberships (user can have multiple pending memberships)
        $pendingMemberships = $graduate->alumniMemberships()
            ->whereIn('status', ['pending', 'paid'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get membership pricing
        $membershipPricing = AlumniMembership::getMembershipPricing();

        if ($request->ajax()) {
            return view('graduate.partials.alumni-membership', compact('currentMembership', 'membershipHistory', 'pendingMemberships', 'membershipPricing', 'graduate'));
        }
        
        return view('graduate.alumni-membership', compact('currentMembership', 'membershipHistory', 'pendingMemberships', 'membershipPricing', 'graduate'));
    }

    public function storeAlumniMembership(Request $request)
    {
        try {
        $user = auth()->user();
        $graduate = $user->graduate;

        $validated = $request->validate([
            'membership_type' => 'required|in:lifetime,yearbook',
            'payment_method' => 'required|in:gcash,paymaya,bank_transfer',
            'payment_reference' => 'nullable|string|max:255',
            
            // Personal Information
            'full_name' => 'required|string|max:255',
            'student_id' => 'nullable|string|max:50',
            'course_degree' => 'required|string|max:255',
            'batch_year' => 'required|integer|min:1900|max:' . date('Y'),
            'date_of_birth' => 'required|date|before:tomorrow',
            'gender' => 'required|in:male,female,other',
            'contact_number' => 'required|string|max:20',
            'email_address' => 'required|email|max:255',
            'address' => 'required|string|max:500',
            
            // Professional Information
            'current_occupation' => 'required|string|max:255',
            'company_organization' => 'nullable|string|max:255',
            'position_job_title' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'work_address' => 'nullable|string|max:500',
            'years_experience' => 'nullable|string|max:20',
            
            // Additional Details
            'skills_expertise' => 'nullable|string|max:1000',
            'achievements_awards' => 'nullable|string|max:1000',
            'volunteer_mentor' => 'nullable|in:yes,maybe,no',
            'preferred_activities' => 'nullable|array',
            'preferred_activities.*' => 'string|in:networking_events,community_service,job_fairs,mentoring,workshops,reunions',
            'membership_reason' => 'required|string|max:1000',
        ]);

        // Check if user already has a pending membership of the same type
        $existingPending = $graduate->alumniMemberships()
            ->where('membership_type', $validated['membership_type'])
            ->whereIn('status', ['pending', 'paid'])
            ->first();

        if ($existingPending) {
            return back()->with('error', 'You already have a pending application for this membership type. Please wait for your current application to be reviewed.');
        }

        // Get membership pricing
        $pricing = AlumniMembership::getMembershipPricing()[$validated['membership_type']];
        
        // Calculate membership dates
        $startDate = now()->toDateString();
        // Yearbook has duration 0, so give it a long-term validity (like lifetime)
        $duration = $pricing['duration'] == 0 ? 36500 : $pricing['duration'];
        $endDate = now()->addDays($duration)->toDateString();

        // Update graduate information with comprehensive data
        $graduate->update([
            // Personal Information
            'first_name' => explode(' ', $validated['full_name'])[0] ?? '',
            'last_name' => explode(' ', $validated['full_name'])[count(explode(' ', $validated['full_name'])) - 1] ?? '',
            'student_id' => $validated['student_id'],
            'program' => $validated['course_degree'],
            'batch_year' => $validated['batch_year'],
            'birth_date' => $validated['date_of_birth'],
            'gender' => $validated['gender'],
            'contact_number' => $validated['contact_number'],
            'present_address' => $validated['address'],
            
            // Professional Information
            'current_position' => $validated['current_occupation'],
            'current_company' => $validated['company_organization'],
            'employment_sector' => $validated['industry'],
            'work_location' => $validated['work_address'],
            'skills' => $validated['skills_expertise'],
        ]);

        // Create comprehensive membership record
        $membership = AlumniMembership::create([
            'graduate_id' => $graduate->id,
            'membership_type' => $validated['membership_type'],
            'amount' => $pricing['amount'],
            'payment_method' => $validated['payment_method'],
            'payment_reference' => $validated['payment_reference'],
            'status' => 'pending',
            'membership_start_date' => $startDate,
            'membership_end_date' => $endDate,
            
            // Personal Information
            'full_name' => $validated['full_name'],
            'student_id' => $validated['student_id'],
            'course_degree' => $validated['course_degree'],
            'batch_year' => $validated['batch_year'],
            'date_of_birth' => $validated['date_of_birth'],
            'gender' => $validated['gender'],
            'contact_number' => $validated['contact_number'],
            'email_address' => $validated['email_address'],
            'address' => $validated['address'],
            
            // Professional Information
            'current_occupation' => $validated['current_occupation'],
            'company_organization' => $validated['company_organization'],
            'position_job_title' => $validated['position_job_title'],
            'industry' => $validated['industry'],
            'work_address' => $validated['work_address'],
            'years_experience' => $validated['years_experience'],
            
            // Additional Details
            'skills_expertise' => $validated['skills_expertise'],
            'achievements_awards' => $validated['achievements_awards'],
            'volunteer_mentor' => $validated['volunteer_mentor'],
            'preferred_activities' => $validated['preferred_activities'] ?? [],
            'membership_reason' => $validated['membership_reason'],
        ]);

        return redirect()->route('graduate.alumni-membership')
            ->with('success', 'Thank you for your purchase! Your membership application has been submitted and will be reviewed by the administrator. Please upload your payment proof to complete the process.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in storeAlumniMembership: ' . json_encode($e->errors()));
            return back()->withInput()->withErrors($e->errors())->with('error', 'Please fix the validation errors and try again.');
        } catch (\Exception $e) {
            \Log::error('Error in storeAlumniMembership: ' . $e->getMessage());
            return back()->withInput()->with('error', 'An error occurred. Please try again.');
        }
    }

    public function uploadPaymentProof(Request $request, AlumniMembership $membership)
    {
        try {
            // Check if user owns this membership
            if ($membership->graduate_id !== auth()->user()->graduate->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            // Ensure the directory exists
            $directory = 'payment-proofs';
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Store the payment proof
            $path = $validated['payment_proof']->store($directory, 'public');
            
            if (!$path) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to store the uploaded file.'
                ], 500);
            }
            
            // Update membership
            $membership->update([
                'payment_proof' => $path,
                'status' => 'paid',
                'payment_date' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment proof uploaded successfully. Your membership is now under review.',
                'payment_proof_url' => asset('storage/' . $path)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Payment proof upload error: ' . $e->getMessage());
            \Log::error('Payment proof upload error trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading the payment proof: ' . $e->getMessage()
            ], 500);
        }
    }

    public function renewAlumniId(Request $request)
    {
        try {
        $user = auth()->user();
        $graduate = $user->graduate;
        
            // Validate the request
            $validated = $request->validate([
                'membership_type' => 'required|in:lifetime,yearbook',
                'full_name' => 'required|string|max:255',
                'student_id' => 'required|string|max:50',
                'course_degree' => 'required|string|max:255',
                'batch_year' => 'required|integer|min:1950|max:' . (date('Y') + 10),
                'date_of_birth' => 'required|date|before:tomorrow',
                'gender' => 'required|in:Male,Female,Other',
                'contact_number' => 'required|string|max:20',
                'email_address' => 'required|email|max:255',
                'address' => 'required|string|max:500',
                'current_occupation' => 'required|string|max:255',
                'company_organization' => 'nullable|string|max:255',
                'position_job_title' => 'nullable|string|max:255',
                'industry' => 'nullable|string|max:255',
                'work_address' => 'nullable|string|max:500',
                'years_experience' => 'nullable|integer|min:0',
                'skills_expertise' => 'nullable|string',
                'achievements_awards' => 'nullable|string',
                'volunteer_mentor' => 'nullable|boolean',
                'preferred_activities' => 'nullable|array',
                'membership_reason' => 'required|string|max:1000',
            ]);

            // Get membership pricing
            $pricing = AlumniMembership::getMembershipPricing()[$validated['membership_type']];
            
            // Calculate membership dates
            $startDate = now()->toDateString();
            $duration = $pricing['duration'] == 0 ? 36500 : $pricing['duration'];
            $endDate = now()->addDays($duration)->toDateString();

            // Update graduate information
            $graduate->update([
                'first_name' => explode(' ', $validated['full_name'])[0] ?? '',
                'last_name' => explode(' ', $validated['full_name'])[count(explode(' ', $validated['full_name'])) - 1] ?? '',
                'student_id' => $validated['student_id'],
                'program' => $validated['course_degree'],
                'batch_year' => $validated['batch_year'],
                'birth_date' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'contact_number' => $validated['contact_number'],
                'present_address' => $validated['address'],
                'current_position' => $validated['current_occupation'],
                'current_company' => $validated['company_organization'],
                'employment_sector' => $validated['industry'],
                'work_location' => $validated['work_address'],
                'skills' => $validated['skills_expertise'],
            ]);

            // Create new membership record for renewal (FREE renewal)
            $membership = AlumniMembership::create([
                'graduate_id' => $graduate->id,
                'membership_type' => $validated['membership_type'],
                'amount' => 0, // FREE renewal
                'payment_method' => null,
                'payment_reference' => null,
                'status' => 'pending', // Admin needs to verify
                'membership_start_date' => $startDate,
                'membership_end_date' => $endDate,
                
                // Personal Information
                'full_name' => $validated['full_name'],
                'student_id' => $validated['student_id'],
                'course_degree' => $validated['course_degree'],
                'batch_year' => $validated['batch_year'],
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'contact_number' => $validated['contact_number'],
                'email_address' => $validated['email_address'],
                'address' => $validated['address'],
                
                // Professional Information
                'current_occupation' => $validated['current_occupation'],
                'company_organization' => $validated['company_organization'],
                'position_job_title' => $validated['position_job_title'],
                'industry' => $validated['industry'],
                'work_address' => $validated['work_address'],
                'years_experience' => $validated['years_experience'],
                
                // Additional Details
                'skills_expertise' => $validated['skills_expertise'],
                'achievements_awards' => $validated['achievements_awards'],
                'volunteer_mentor' => $validated['volunteer_mentor'],
                'preferred_activities' => $validated['preferred_activities'] ?? [],
                'membership_reason' => $validated['membership_reason'],
            ]);

            return redirect()->route('graduate.alumni-membership')
                ->with('success', 'Thank you for renewing your Alumni ID! Your renewal request has been submitted and will be reviewed by the administrator. No payment is required for renewal.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in renewAlumniId: ' . json_encode($e->errors()));
            return back()->withInput()->withErrors($e->errors())->with('error', 'Please fix the validation errors and try again.');
        } catch (\Exception $e) {
            \Log::error('Error in renewAlumniId: ' . $e->getMessage());
            return back()->withInput()->with('error', 'An error occurred. Please try again.');
        }
    }

    public function profile(Request $request)
    {
        // Redirect to enhanced profile form
        return redirect()->route('graduate.profile.enhanced');
    }

    public function enhancedProfile()
    {
        $user = auth()->user();
        $graduate = $user->graduate;
        
        // If no graduate record exists, create one with basic info
        if (!$graduate) {
                $graduate = Graduate::create([
                    'user_id' => $user->id,
                    'student_id' => 'TEMP-' . $user->id,
                    'program' => 'To be updated',
                    'batch_year' => date('Y'),
                    'graduation_date' => now(),
                'first_name' => $user->name,
                    'last_name' => '',
                'current_status' => 'graduate',
            ]);
        }
        
        return view('graduate.partials.enhanced-profile', compact('graduate'));
    }


    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $graduate = $user->graduate;

            $request->validate([
            // Basic information
                'student_id' => 'nullable|string|max:50',
                'program' => 'nullable|string|max:255',
                'batch_year' => 'nullable|integer|min:2000|max:2030',
                'graduation_date' => 'nullable|date',
            'graduation_year' => 'nullable|integer|min:1950|max:2030',
            'contact_number' => 'nullable|string|max:20',
            'linkedin_profile' => 'nullable|url|max:255',
            'bio' => 'nullable|string|max:1000',
            
            // Personal Information
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'middle_initial' => 'nullable|string|max:1',
            'extension' => 'nullable|string|max:10',
            'gender' => 'nullable|string|in:male,female,other',
            'birth_date' => 'nullable|date',
            'place_of_birth' => 'nullable|string|max:255',
            'civil_status' => 'nullable|string|in:single,married,widowed,divorced,separated',
            'nationality' => 'nullable|string|max:100',
            'religion' => 'nullable|string|max:100',
            'blood_type' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            
            // Family Information
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            
            // Address Information
            'present_address' => 'nullable|string|max:1000',
                'municipality_city' => 'nullable|string|max:100',
            'province_region' => 'nullable|string|max:100',
                'barangay' => 'nullable|string|max:100',
                'zip_code' => 'nullable|string|max:10',
            'permanent_address' => 'nullable|string|max:1000',
                'permanent_city' => 'nullable|string|max:100',
            'permanent_province' => 'nullable|string|max:100',
                'permanent_barangay' => 'nullable|string|max:100',
                'permanent_zip_code' => 'nullable|string|max:10',
            
            // Status and career information
            'current_status' => 'nullable|string|in:graduate,undergraduate,employed,unemployed,pursuing_higher_education,self_employed',
            'employment_type' => 'nullable|string|in:full_time,part_time,contract,freelance,internship,self_employed',
            'employment_sector' => 'nullable|string|max:100',
            'job_level' => 'nullable|string|in:entry,mid,senior,manager,director,executive',
            'current_position' => 'nullable|string|max:255',
            'current_company' => 'nullable|string|max:255',
            'work_location' => 'nullable|string|max:255',
            'employment_start_date' => 'nullable|date',
            'is_remote_work' => 'nullable|boolean',
            'job_description' => 'nullable|string|max:2000',
            
            // Education information
            'pursuing_degree' => 'nullable|string|max:255',
            'institution_name' => 'nullable|string|max:255',
            'expected_graduation' => 'nullable|date',
            
            // Career development
            'career_goals' => 'nullable|string|max:2000',
            'skills' => 'nullable|string|max:2000',
            'interests' => 'nullable|string|max:2000',
        ]);

        // Update user's name in users table if name fields are provided
        if ($request->first_name || $request->last_name) {
            $nameParts = array_filter([
                $request->first_name,
                $request->middle_name,
                $request->last_name,
                $request->extension
            ]);
            $fullName = trim(implode(' ', $nameParts));
            if ($fullName) {
                $user->update(['name' => $fullName]);
            }
        }

        // Update or create graduate profile with all fields
        $data = [
            'student_id' => $request->student_id,
            'program' => $request->program,
            'batch_year' => $request->batch_year,
            'graduation_date' => $request->graduation_date,
            'graduation_year' => $request->graduation_year,
            'contact_number' => $request->contact_number,
            'linkedin_profile' => $request->linkedin_profile,
            'bio' => $request->bio,
            
            // Personal Information
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'middle_initial' => $request->middle_initial,
            'extension' => $request->extension,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'place_of_birth' => $request->place_of_birth,
            'civil_status' => $request->civil_status,
            'nationality' => $request->nationality,
            'religion' => $request->religion,
            'blood_type' => $request->blood_type,
            
            // Family Information
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name,
            
            // Address Information
            'present_address' => $request->present_address,
            'municipality_city' => $request->municipality_city,
            'province_region' => $request->province_region,
            'barangay' => $request->barangay,
            'zip_code' => $request->zip_code,
            'permanent_address' => $request->permanent_address,
            'permanent_city' => $request->permanent_city,
            'permanent_province' => $request->permanent_province,
            'permanent_barangay' => $request->permanent_barangay,
            'permanent_zip_code' => $request->permanent_zip_code,
            
            // Status and career information
            'current_status' => $request->current_status,
            'employment_type' => $request->employment_type,
            'employment_sector' => $request->employment_sector,
            'job_level' => $request->job_level,
            'current_position' => $request->current_position,
            'current_company' => $request->current_company,
            'work_location' => $request->work_location,
            'employment_start_date' => $request->employment_start_date,
            'is_remote_work' => $request->is_remote_work,
            'job_description' => $request->job_description,
            'pursuing_degree' => $request->pursuing_degree,
            'institution_name' => $request->institution_name,
            'expected_graduation' => $request->expected_graduation,
            'career_goals' => $request->career_goals,
            'skills' => $request->skills,
            'interests' => $request->interests,
        ];

        // Remove null values
        $data = array_filter($data, function($value) {
            return $value !== null && $value !== '';
        });

            if ($graduate) {
                $graduate->update($data);
            } else {
                $data['user_id'] = $user->id;
            $data['student_id'] = $data['student_id'] ?? 'TEMP-' . $user->id;
            $data['program'] = $data['program'] ?? 'To be updated';
            $data['batch_year'] = $data['batch_year'] ?? date('Y');
            $data['graduation_date'] = $data['graduation_date'] ?? now();
            $data['current_status'] = $data['current_status'] ?? 'graduate';
            Graduate::create($data);
        }

        return redirect()->route('graduate.profile.enhanced')->with('success', 'Profile updated successfully!');
    }


    public function employment(Request $request)
    {
        $graduate = auth()->user()->graduate;
        $employmentRecords = $graduate ? $graduate->employmentRecords : collect();
        
        if ($request->ajax()) {
            return view('graduate.partials.employment', compact('graduate', 'employmentRecords'));
        }
        
        return view('graduate.employment', compact('graduate', 'employmentRecords'));
    }

    public function updateEmployment(Request $request)
    {
        $graduate = auth()->user()->graduate;

        $request->validate([
            'is_employed' => 'required|boolean',
            'current_position' => 'nullable|string|max:255',
            'current_company' => 'nullable|string|max:255',
            'employment_start_date' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
        ]);

        if ($graduate) {
            $graduate->update([
                'is_employed' => $request->is_employed,
                'current_position' => $request->current_position,
                'current_company' => $request->current_company,
                'employment_start_date' => $request->employment_start_date,
                'salary' => $request->salary,
            ]);
        }

        return redirect()->route('graduate.employment')->with('success', 'Employment information updated successfully!');
    }

    public function resume(Request $request)
    {
        $graduate = auth()->user()->graduate;
        $resumes = $graduate ? $graduate->resumes : collect();
        
        if ($request->ajax()) {
            return view('graduate.partials.resume', compact('graduate', 'resumes'));
        }
        
        return view('graduate.resume', compact('graduate', 'resumes'));
    }

    public function generateResume(Request $request)
    {
        $graduate = auth()->user()->graduate;

        if (!$graduate) {
            return redirect()->route('graduate.profile')->with('error', 'Please complete your profile first.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'template_type' => 'required|string|in:standard,modern,creative,compact',
        ]);

        // Generate resume content based on graduate data
        $content = $this->generateResumeContent($graduate, $request->template_type);

        Resume::create([
            'graduate_id' => $graduate->id,
            'title' => $request->title,
            'template_type' => $request->template_type,
            'content' => $content,
            'is_active' => true,
        ]);

        return redirect()->route('graduate.resume')->with('success', 'Resume generated successfully!');
    }

    public function jobs(Request $request)
    {
        $jobPostings = JobPosting::where('is_active', true)
            ->whereIn('status', ['approved', 'published']) // Show approved and published jobs
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        if ($request->ajax()) {
            return view('graduate.partials.jobs', compact('jobPostings'));
        }

        return view('graduate.jobs', compact('jobPostings'));
    }

    public function uploadProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = auth()->user();
        $graduate = $user->graduate;

        if (!$graduate) {
            return response()->json([
                'success' => false,
                'message' => 'Graduate profile not found'
            ], 404);
        }

        try {
            // Delete old profile picture if exists
            if ($graduate->profile_picture && \Storage::exists('public/' . $graduate->profile_picture)) {
                \Storage::delete('public/' . $graduate->profile_picture);
            }

            // Store new profile picture
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            // Normalize path separators for Windows environments
            $path = str_replace('\\', '/', $path);
            
            // Update graduate record
            $graduate->update(['profile_picture' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Profile picture updated successfully',
                'profile_picture_url' => asset('storage/' . $path)
            ]);

        } catch (\Exception $e) {
            \Log::error('Error uploading profile picture:', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error uploading profile picture. Please try again.'
            ], 500);
        }
    }

    public function viewResume($id)
    {
        $resume = Resume::findOrFail($id);
        $graduate = $resume->graduate;

        if ($resume->template_type === 'creative') {
            return view('graduate.resume-templates.creative', compact('graduate', 'resume'));
        } elseif ($resume->template_type === 'compact') {
            return view('graduate.resume-templates.compact', compact('graduate', 'resume'));
        }

        // Handle other template types
        return view('graduate.resume-templates.standard', compact('graduate', 'resume'));
    }

    public function updateResume(Request $request, $id)
    {
        $resume = Resume::findOrFail($id);
        $graduate = $resume->graduate;
        $user = auth()->user();

        // Check if the resume belongs to the current user
        if ($resume->graduate_id !== $user->graduate->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to update this resume.'
            ], 403);
        }

        try {
            // Log the request data for debugging
            \Log::info('Resume update request data:', $request->all());

            // Update graduate profile fields
            $graduateData = [];
            $userData = [];

            if ($request->filled('first_name')) {
                $graduateData['first_name'] = $request->first_name;
            }
            if ($request->filled('last_name')) {
                $graduateData['last_name'] = $request->last_name;
            }
            if ($request->filled('current_position')) {
                $graduateData['current_position'] = $request->current_position;
            }
            if ($request->filled('contact_number')) {
                $graduateData['contact_number'] = $request->contact_number;
            }
            if ($request->filled('linkedin_profile')) {
                $graduateData['linkedin_profile'] = $request->linkedin_profile;
            }
            if ($request->filled('bio')) {
                $graduateData['bio'] = $request->bio;
            }
            if ($request->filled('email')) {
                $userData['email'] = $request->email;
            }

            // Handle additional resume content fields
            $resumeData = [];
            if ($request->filled('skills')) {
                $resumeData['skills'] = (string) $request->skills;
            }
            if ($request->filled('software')) {
                $resumeData['software'] = (string) $request->software;
            }
            if ($request->filled('languages')) {
                $resumeData['languages'] = (string) $request->languages;
            }
            if ($request->filled('work_experience')) {
                $resumeData['work_experience'] = (string) $request->work_experience;
            }
            if ($request->filled('education')) {
                $resumeData['education'] = (string) $request->education;
            }
            if ($request->filled('references')) {
                $resumeData['references'] = (string) $request->references;
            }

            \Log::info('Data to be updated:', [
                'graduateData' => $graduateData,
                'userData' => $userData,
                'resumeData' => $resumeData
            ]);

            // Update resume content
            if (!empty($resumeData)) {
                try {
                    $currentContent = json_decode($resume->content, true) ?? [];
                    if (!is_array($currentContent)) {
                        $currentContent = [];
                    }
                    $updatedContent = array_merge($currentContent, $resumeData);
                    $resume->update(['content' => json_encode($updatedContent)]);
                    \Log::info('Resume content updated:', ['content' => $updatedContent]);
                } catch (\Exception $e) {
                    \Log::error('Error updating resume content:', [
                        'error' => $e->getMessage(),
                        'resume_id' => $id
                    ]);
                }
            }

            // Update user data
            if (!empty($userData)) {
                $user->update($userData);
                \Log::info('User data updated:', $userData);
            }

            // Update graduate data
            if (!empty($graduateData)) {
                $graduate->update($graduateData);
                \Log::info('Graduate data updated:', $graduateData);
            }

            return response()->json([
                'success' => true,
                'message' => 'Resume updated successfully!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating resume:', [
                'error' => $e->getMessage(),
                'resume_id' => $id,
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating resume. Please try again.'
            ], 500);
        }
    }

    public function deleteResume($id)
    {
        $resume = Resume::findOrFail($id);
        $graduate = auth()->user()->graduate;

        // Check if the resume belongs to the current user
        if ($resume->graduate_id !== $graduate->id) {
            return redirect()->route('graduate.resume')->with('error', 'You are not authorized to delete this resume.');
        }

        try {
            $resume->delete();
            return redirect()->route('graduate.resume')->with('success', 'Resume deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error deleting resume:', [
                'error' => $e->getMessage(),
                'resume_id' => $id,
                'user_id' => auth()->id()
            ]);
            return redirect()->route('graduate.resume')->with('error', 'Error deleting resume. Please try again.');
        }
    }

    private function generateResumeContent($graduate, $templateType)
    {
        $content = [
            'personal_info' => [
                'name' => $graduate->user->name,
                'email' => $graduate->user->email,
                'phone' => $graduate->phone,
                'address' => $graduate->address,
                'linkedin' => $graduate->linkedin_profile,
            ],
            'education' => [
                'program' => $graduate->program,
                'institution' => 'University of Science and Technology of Southern Philippines - Balubal',
                'batch_year' => $graduate->batch_year,
                'graduation_date' => $graduate->graduation_date->format('Y'),
            ],
            'employment' => [
                'is_employed' => $graduate->is_employed,
                'current_position' => $graduate->current_position,
                'current_company' => $graduate->current_company,
                'employment_start_date' => $graduate->employment_start_date,
                'salary' => $graduate->salary,
            ],
            'employment_history' => $graduate->employmentRecords->map(function ($record) {
                return [
                    'position' => $record->position,
                    'company' => $record->company_name,
                    'start_date' => $record->start_date->format('M Y'),
                    'end_date' => $record->end_date ? $record->end_date->format('M Y') : 'Present',
                    'description' => $record->job_description,
                ];
            })->toArray(),
            'bio' => $graduate->bio,
            'template_type' => $templateType,
        ];

        return json_encode($content);
    }

    public function storeJob(Request $request)
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

        $jobPosting = JobPosting::create([
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
            'status' => 'pending', // New jobs need admin approval
            'is_active' => true,
        ]);

        // Fire event for real-time notification
        event(new NewJobPostingSubmitted($jobPosting));

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Job posted successfully! It will be reviewed by an admin before being published.',
                'job_id' => $jobPosting->id
            ]);
        }

        return redirect()->route('graduate.jobs')->with('success', 'Job posted successfully! It will be reviewed by an admin before being published.');
    }

    public function jobDetails($id)
    {
        $job = JobPosting::where('id', $id)
            ->where('is_active', true)
            ->whereIn('status', ['approved', 'published'])
            ->with('postedBy')
            ->firstOrFail();

        $html = view('graduate.partials.job-details', compact('job'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    public function applyToJob(Request $request)
    {
        $request->validate([
            'job_id' => 'required|exists:job_postings,id',
            'cover_letter' => 'nullable|string|max:1000',
        ]);

        $job = JobPosting::where('id', $request->job_id)
            ->where('is_active', true)
            ->whereIn('status', ['approved', 'published'])
            ->firstOrFail();

        // Check if user already applied
        $existingApplication = \DB::table('job_applications')
            ->where('user_id', auth()->id())
            ->where('job_posting_id', $request->job_id)
            ->first();

        if ($existingApplication) {
            return response()->json([
                'success' => false,
                'message' => 'You have already applied for this job.'
            ]);
        }

        // Create job application
        \DB::table('job_applications')->insert([
            'user_id' => auth()->id(),
            'job_posting_id' => $request->job_id,
            'cover_letter' => $request->cover_letter,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully!'
        ]);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $user = auth()->user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The current password is incorrect.',
                    'errors' => ['current_password' => ['The current password is incorrect.']]
                ], 422);
            }
            return back()->withErrors(['current_password' => 'The current password is incorrect.'])->withInput();
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully!'
            ]);
        }

        return redirect()->route('graduate.profile.enhanced')->with('success', 'Password changed successfully!');
    }

    public function graduationApplication()
    {
        $user = auth()->user();
        $graduate = $user->graduate;
        
        return view('graduate.graduation-application', compact('graduate'));
    }

    public function printGraduationApplication()
    {
        return view('graduate.print-graduation-application');
    }

    public function storeGraduationApplication(Request $request)
    {
        try {
            $user = auth()->user();
            $graduate = $user->graduate;

        $validated = $request->validate([
            'application_date' => 'required|date',
            'application_type' => 'required|in:degree,diploma',
            'major_in' => 'nullable|string|max:255',
            'campus' => 'required|string|max:255',
            'city_province' => 'required|string|max:255',
            'college_unit_department' => 'required|string|max:255',
            'commencement_date' => 'required|date',
            'last_semester' => 'required|in:semester,summer',
            'school_year' => 'required|string|max:20',
            'subjects' => 'required|array|min:1',
            'subjects.*.code' => 'nullable|string|max:20',
            'subjects.*.title' => 'nullable|string|max:255',
            'subjects.*.units' => 'nullable|numeric|min:0',
            'subjects.*.instructor' => 'nullable|string|max:255',
            'subjects.*.signature' => 'nullable|string|max:255',
            'total_units' => 'required|numeric|min:0',
            'total_instructor' => 'nullable|string|max:255',
            'total_signature' => 'nullable|string|max:255',
            'diploma_name' => 'required|string|max:255',
            'diploma_address' => 'required|string|max:500',
            'diploma_contact' => 'required|string|max:20',
        ]);

        // Check if user already has a pending application
        $existingApplication = $graduate->graduationApplications()
            ->where('status', 'pending')
            ->first();

        if ($existingApplication) {
            return back()->with('error', 'You already have a pending graduation application.');
        }

        // Create the graduation application
        $application = \App\Models\GraduationApplication::create([
            'graduate_id' => $graduate->id,
            'application_type' => $validated['application_type'],
            'major_in' => $validated['major_in'],
            'campus' => $validated['campus'],
            'city_province' => $validated['city_province'],
            'college_unit_department' => $validated['college_unit_department'],
            'last_semester' => $validated['last_semester'],
            'school_year' => $validated['school_year'],
            'subject_load' => $validated['subjects'],
            'diploma_name' => $validated['diploma_name'],
            'diploma_address' => $validated['diploma_address'],
            'diploma_contact' => $validated['diploma_contact'],
            'status' => 'pending',
        ]);

            return redirect()->route('graduate.graduation-application')
                ->with('success', 'Your graduation application has been submitted successfully. Please wait for approval from the registrar.');
        } catch (\Exception $e) {
            \Log::error('Error creating graduation application: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Failed to submit graduation application: ' . $e->getMessage());
        }
    }
}
