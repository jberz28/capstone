<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GraduateController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlumniSurveyController;
use App\Http\Controllers\AnnouncementController;

// Public routes
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
        // Graduate routes
        Route::prefix('graduate')->middleware('role:graduate')->group(function () {
            Route::get('/dashboard', [GraduateController::class, 'dashboard'])->name('graduate.dashboard');
            Route::get('/profile', [GraduateController::class, 'profile'])->name('graduate.profile');
            Route::get('/profile/enhanced', [GraduateController::class, 'enhancedProfile'])->name('graduate.profile.enhanced');
            Route::post('/profile', [GraduateController::class, 'updateProfile'])->name('graduate.profile.update');
            Route::post('/profile/picture', [GraduateController::class, 'uploadProfilePicture'])->name('graduate.profile.picture');
            Route::post('/profile/change-password', [GraduateController::class, 'changePassword'])->name('graduate.profile.change-password');
            Route::get('/resume/{id}/view', [GraduateController::class, 'viewResume'])->name('graduate.resume.view');
            Route::post('/resume/{id}/update', [GraduateController::class, 'updateResume'])->name('graduate.resume.update');
            Route::delete('/resume/{id}', [GraduateController::class, 'deleteResume'])->name('graduate.resume.delete');
            Route::get('/employment', [GraduateController::class, 'employment'])->name('graduate.employment');
            Route::post('/employment', [GraduateController::class, 'updateEmployment'])->name('graduate.employment.update');
            Route::get('/resume', [GraduateController::class, 'resume'])->name('graduate.resume');
            Route::post('/resume/generate', [GraduateController::class, 'generateResume'])->name('graduate.resume.generate');
            Route::get('/jobs', [GraduateController::class, 'jobs'])->name('graduate.jobs');
            Route::get('/jobs/{job}/details', [GraduateController::class, 'jobDetails'])->name('graduate.jobs.details');
            Route::post('/jobs/apply', [GraduateController::class, 'applyToJob'])->name('graduate.jobs.apply');
            Route::get('/alumni-activities', [GraduateController::class, 'alumniActivities'])->name('graduate.alumni-activities');
        Route::get('/alumni-activities/{alumniActivity}/details', [GraduateController::class, 'alumniActivityDetails'])->name('graduate.alumni-activities.details');
            Route::get('/alumni-membership', [GraduateController::class, 'alumniMembership'])->name('graduate.alumni-membership');
            Route::post('/alumni-membership', [GraduateController::class, 'storeAlumniMembership'])->name('graduate.alumni-membership.store');
            Route::post('/alumni-membership/{membership}/payment', [GraduateController::class, 'uploadPaymentProof'])->name('graduate.alumni-membership.payment');
            Route::post('/alumni-membership/renew', [GraduateController::class, 'renewAlumniId'])->name('graduate.alumni-membership.renew');
        // Alumni Survey - graduate
        Route::get('/survey', [AlumniSurveyController::class, 'create'])->name('graduate.survey.index');
        Route::get('/survey/create', [AlumniSurveyController::class, 'create'])->name('graduate.survey.create');
        Route::post('/survey', [AlumniSurveyController::class, 'store'])->name('graduate.survey.store');
        Route::get('/survey/thank-you', [AlumniSurveyController::class, 'thankyou'])->name('graduate.survey.thankyou');
        
        // Announcements routes
        Route::get('/announcements', [AnnouncementController::class, 'index'])->name('graduate.announcements');
        Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show'])->name('graduate.announcements.show');
        
        // Graduation Application routes
        Route::get('/graduation-application', [GraduateController::class, 'graduationApplication'])->name('graduate.graduation-application');
        Route::post('/graduation-application', [GraduateController::class, 'storeGraduationApplication'])->name('graduate.graduation-application.store');
        Route::get('/graduation-application/print', [GraduateController::class, 'printGraduationApplication'])->name('graduate.graduation-application.print');
        
            Route::get('/alumni-membership/debug', function() {
                return response()->json([
                    'storage_exists' => \Storage::disk('public')->exists('payment-proofs'),
                    'storage_writable' => is_writable(storage_path('app/public')),
                    'public_storage_link' => file_exists(public_path('storage')),
                    'payment_proofs_dir' => \Storage::disk('public')->exists('payment-proofs'),
                ]);
            })->name('graduate.alumni-membership.debug');
        Route::post('/jobs', [GraduateController::class, 'storeJob'])->name('graduate.jobs.store');
        });
    
    // Staff routes
    Route::prefix('staff')->middleware('role:staff')->group(function () {
        Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('staff.dashboard');
        Route::get('/graduates', [StaffController::class, 'graduates'])->name('staff.graduates');
        Route::get('/graduates/{graduate}', [StaffController::class, 'showGraduate'])->name('staff.graduates.show');
        Route::get('/career-support', [StaffController::class, 'careerSupport'])->name('staff.career-support');
        Route::get('/graduates/{graduate}/details', [StaffController::class, 'graduateDetails'])->name('staff.graduates.details');
        Route::post('/graduates/{graduate}/verify', [StaffController::class, 'verifyGraduate'])->name('staff.graduates.verify');
        Route::get('/job-postings', [StaffController::class, 'jobPostings'])->name('staff.job-postings');
        Route::get('/job-postings/{jobPosting}/details', [StaffController::class, 'jobDetails'])->name('staff.job-postings.details');
        Route::get('/job-postings/{jobPosting}/edit', [StaffController::class, 'editJobPosting'])->name('staff.job-postings.edit');
        Route::post('/job-postings', [StaffController::class, 'storeJobPosting'])->name('staff.job-postings.store');
        Route::put('/job-postings/{jobPosting}', [StaffController::class, 'updateJobPosting'])->name('staff.job-postings.update');
        Route::post('/job-postings/{jobPosting}/status', [StaffController::class, 'updateJobStatus'])->name('staff.job-postings.status');
        
        // Staff alumni activities
        Route::get('/alumni-activities', [StaffController::class, 'alumniActivities'])->name('staff.alumni-activities');
        Route::get('/alumni-activities/create', [StaffController::class, 'createAlumniActivity'])->name('staff.alumni-activities.create');
        Route::post('/alumni-activities', [StaffController::class, 'storeAlumniActivity'])->name('staff.alumni-activities.store');
        Route::get('/alumni-activities/{alumniActivity}/edit', [StaffController::class, 'editAlumniActivity'])->name('staff.alumni-activities.edit');
        Route::patch('/alumni-activities/{alumniActivity}', [StaffController::class, 'updateAlumniActivity'])->name('staff.alumni-activities.update');
        Route::delete('/alumni-activities/{alumniActivity}', [StaffController::class, 'deleteAlumniActivity'])->name('staff.alumni-activities.delete');
        
        Route::get('/reports', [StaffController::class, 'reports'])->name('staff.reports');
        Route::post('/reports/generate', [StaffController::class, 'generateReport'])->name('staff.reports.generate');
        Route::get('/alumni', [StaffController::class, 'alumni'])->name('staff.alumni');
        // Staff view surveys
        Route::get('/surveys', [AlumniSurveyController::class, 'indexStaff'])->name('staff.surveys.index');
        Route::get('/surveys/{survey}', [AlumniSurveyController::class, 'showStaff'])->name('staff.surveys.show');
        
        // Staff announcements
        Route::get('/announcements', [AnnouncementController::class, 'indexStaff'])->name('staff.announcements.index');
        Route::get('/announcements/create', [AnnouncementController::class, 'create'])->name('staff.announcements.create');
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('staff.announcements.store');
        Route::get('/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('staff.announcements.edit');
        Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('staff.announcements.update');
        Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('staff.announcements.destroy');
        Route::post('/announcements/{announcement}/publish', [AnnouncementController::class, 'publish'])->name('staff.announcements.publish');
        Route::post('/announcements/{announcement}/archive', [AnnouncementController::class, 'archive'])->name('staff.announcements.archive');
        
        // Staff graduation applications
        Route::get('/graduation-applications', [StaffController::class, 'graduationApplications'])->name('staff.graduation-applications');
        Route::get('/graduation-applications/{application}', [StaffController::class, 'showGraduationApplication'])->name('staff.graduation-applications.show');
        Route::post('/graduation-applications/{application}/approve', [StaffController::class, 'approveGraduationApplication'])->name('staff.graduation-applications.approve');
        Route::post('/graduation-applications/{application}/reject', [StaffController::class, 'rejectGraduationApplication'])->name('staff.graduation-applications.reject');
    });
    
    // Admin routes
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
        Route::get('/users/{user}/details', [AdminController::class, 'userDetails'])->name('admin.users.details');
        Route::post('/users/{user}/verify', [AdminController::class, 'verifyUser'])->name('admin.users.verify');
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
        Route::get('/data-monitoring', [AdminController::class, 'dataMonitoring'])->name('admin.data-monitoring');
        Route::get('/job-postings', [AdminController::class, 'jobPostings'])->name('admin.job-postings');
        Route::get('/job-postings/create', [AdminController::class, 'createJobPosting'])->name('admin.job-postings.create');
        Route::post('/job-postings', [AdminController::class, 'storeJobPosting'])->name('admin.job-postings.store');
        Route::get('/job-postings/{jobPosting}/details', [AdminController::class, 'jobDetails'])->name('admin.job-postings.details');
        Route::post('/job-postings/{jobPosting}/status', [AdminController::class, 'updateJobStatus'])->name('admin.job-postings.status');
        Route::delete('/job-postings/{jobPosting}/delete', [AdminController::class, 'deleteJob'])->name('admin.job-postings.delete');
        
        // Admin alumni activities
        Route::get('/alumni-activities', [AdminController::class, 'alumniActivities'])->name('admin.alumni-activities');
        Route::get('/alumni-activities/create', [AdminController::class, 'createAlumniActivity'])->name('admin.alumni-activities.create');
        Route::post('/alumni-activities', [AdminController::class, 'storeAlumniActivity'])->name('admin.alumni-activities.store');
        Route::get('/alumni-activities/{alumniActivity}/edit', [AdminController::class, 'editAlumniActivity'])->name('admin.alumni-activities.edit');
        Route::patch('/alumni-activities/{alumniActivity}', [AdminController::class, 'updateAlumniActivity'])->name('admin.alumni-activities.update');
        Route::delete('/alumni-activities/{alumniActivity}', [AdminController::class, 'deleteAlumniActivity'])->name('admin.alumni-activities.delete');
        Route::post('/alumni-activities/{alumniActivity}/status', [AdminController::class, 'updateAlumniActivityStatus'])->name('admin.alumni-activities.status');
        
        // Admin alumni memberships
        Route::get('/alumni-memberships', [AdminController::class, 'alumniMemberships'])->name('admin.alumni-memberships');
        Route::get('/alumni-memberships/{membership}/details', [AdminController::class, 'membershipDetails'])->name('admin.alumni-memberships.details');
        Route::post('/alumni-memberships/{membership}/confirm-payment', [AdminController::class, 'confirmPayment'])->name('admin.alumni-memberships.confirm-payment');
        Route::post('/alumni-memberships/{membership}/verify', [AdminController::class, 'verifyMembership'])->name('admin.alumni-memberships.verify');
        Route::post('/alumni-memberships/{membership}/reject', [AdminController::class, 'rejectMembership'])->name('admin.alumni-memberships.reject');
        Route::post('/alumni-memberships/{membership}/deliver', [AdminController::class, 'deliverMembership'])->name('admin.alumni-memberships.deliver');
        Route::delete('/alumni-memberships/{membership}', [AdminController::class, 'deleteMembership'])->name('admin.alumni-memberships.delete');
        Route::post('/jobs/{id}/approve', [AdminController::class, 'approveJob'])->name('admin.jobs.approve');
        Route::post('/jobs/{id}/reject', [AdminController::class, 'rejectJob'])->name('admin.jobs.reject');
        // Admin view surveys
        Route::get('/surveys', [AlumniSurveyController::class, 'indexAdmin'])->name('admin.surveys.index');
        Route::get('/surveys/{survey}', [AlumniSurveyController::class, 'showAdmin'])->name('admin.surveys.show');
        Route::delete('/surveys/{survey}', [AlumniSurveyController::class, 'destroy'])->name('admin.surveys.destroy');
        Route::get('/notifications/check', [AdminController::class, 'checkNotifications'])->name('admin.notifications.check');
        Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
        Route::post('/reports/generate', [AdminController::class, 'generateReport'])->name('admin.reports.generate');
        Route::get('/maintenance', [AdminController::class, 'maintenance'])->name('admin.maintenance');
        Route::post('/maintenance/backup', [AdminController::class, 'backup'])->name('admin.maintenance.backup');
        
        // Admin announcements
        Route::get('/announcements', [AnnouncementController::class, 'indexAdmin'])->name('admin.announcements.index');
        Route::get('/announcements/create', [AnnouncementController::class, 'create'])->name('admin.announcements.create');
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('admin.announcements.store');
        Route::get('/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('admin.announcements.edit');
        Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('admin.announcements.update');
        Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('admin.announcements.destroy');
        Route::post('/announcements/{announcement}/publish', [AnnouncementController::class, 'publish'])->name('admin.announcements.publish');
        Route::post('/announcements/{announcement}/archive', [AnnouncementController::class, 'archive'])->name('admin.announcements.archive');
        
        // Admin graduation applications
        Route::get('/graduation-applications', [AdminController::class, 'graduationApplications'])->name('admin.graduation-applications');
        Route::get('/graduation-applications/{application}', [AdminController::class, 'showGraduationApplication'])->name('admin.graduation-applications.show');
        Route::post('/graduation-applications/{application}/approve', [AdminController::class, 'approveGraduationApplication'])->name('admin.graduation-applications.approve');
        Route::post('/graduation-applications/{application}/reject', [AdminController::class, 'rejectGraduationApplication'])->name('admin.graduation-applications.reject');
    });
});
