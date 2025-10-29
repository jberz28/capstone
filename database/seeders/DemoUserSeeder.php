<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Graduate;
use App\Models\JobPosting;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        // Upsert Admin User
        $admin = User::updateOrCreate(
            [
                'email' => 'admin@ustp.edu.ph',
            ],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_verified' => true,
            ]
        );

        // Upsert Staff User
        $staff = User::updateOrCreate(
            [
                'email' => 'staff@ustp.edu.ph',
            ],
            [
                'name' => 'Career Center Staff',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'is_verified' => true,
            ]
        );

        // Upsert Graduate User
        $graduate = User::updateOrCreate(
            [
                'email' => 'graduate@ustp.edu.ph',
            ],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
                'role' => 'graduate',
                'is_verified' => true,
            ]
        );

        // Upsert Graduate Profile
        Graduate::updateOrCreate(
            [
                'user_id' => $graduate->id,
            ],
            [
                'student_id' => '2020-12345',
                'program' => 'Bachelor of Science in Computer Engineering',
                'batch_year' => '2020',
                'graduation_date' => '2024-06-15',
                'phone' => '+63 912 345 6789',
                'address' => 'Cagayan de Oro City, Philippines',
                'linkedin_profile' => 'https://linkedin.com/in/johndoe',
                'bio' => 'Recent graduate with passion for software development and technology innovation.',
                'is_employed' => true,
                'current_position' => 'Software Engineer',
                'current_company' => 'Tech Solutions Inc.',
                'employment_start_date' => '2024-07-01',
                'salary' => 35000.00,
            ]
        );

        // Create additional sample graduates
        $graduates = [
            [
                'name' => 'Maria Santos',
                'email' => 'maria.santos@ustp.edu.ph',
                'student_id' => '2020-12346',
                'program' => 'Bachelor of Science in Information Technology',
                'batch_year' => '2020',
                'graduation_date' => '2024-06-15',
                'phone' => '+63 912 345 6790',
                'address' => 'Iligan City, Philippines',
                'is_employed' => true,
                'current_position' => 'Web Developer',
                'current_company' => 'Digital Agency Co.',
                'employment_start_date' => '2024-08-01',
                'salary' => 32000.00,
            ],
            [
                'name' => 'Carlos Rodriguez',
                'email' => 'carlos.rodriguez@ustp.edu.ph',
                'student_id' => '2021-12347',
                'program' => 'Bachelor of Science in Civil Engineering',
                'batch_year' => '2021',
                'graduation_date' => '2025-06-15',
                'phone' => '+63 912 345 6791',
                'address' => 'Davao City, Philippines',
                'is_employed' => false,
            ],
        ];

        foreach ($graduates as $gradData) {
            $user = User::updateOrCreate(
                [
                    'email' => $gradData['email'],
                ],
                [
                    'name' => $gradData['name'],
                    'password' => Hash::make('password'),
                    'role' => 'graduate',
                ]
            );

            Graduate::updateOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'student_id' => $gradData['student_id'],
                    'program' => $gradData['program'],
                    'batch_year' => $gradData['batch_year'],
                    'graduation_date' => $gradData['graduation_date'],
                    'phone' => $gradData['phone'],
                    'address' => $gradData['address'],
                    'is_employed' => $gradData['is_employed'],
                    'current_position' => $gradData['current_position'] ?? null,
                    'current_company' => $gradData['current_company'] ?? null,
                    'employment_start_date' => $gradData['employment_start_date'] ?? null,
                    'salary' => $gradData['salary'] ?? null,
                ]
            );
        }

        // Create demo job postings
        $jobPostings = [
            [
                'title' => 'Software Developer',
                'description' => 'We are looking for a talented software developer to join our team. You will be responsible for developing and maintaining web applications using modern technologies.',
                'company' => 'Tech Solutions Inc.',
                'location' => 'Cagayan de Oro City',
                'employment_type' => 'Full-time',
                'salary_min' => 30000,
                'salary_max' => 50000,
                'requirements' => 'Bachelor\'s degree in Computer Science or related field, 1-2 years experience in web development, knowledge of PHP, Laravel, JavaScript',
                'benefits' => 'Health insurance, 13th month pay, vacation leave, training opportunities',
                'application_deadline' => now()->addDays(30),
                'status' => 'published',
                'is_active' => true,
            ],
            [
                'title' => 'IT Support Specialist',
                'description' => 'Join our IT team as a support specialist. You will provide technical support to our employees and maintain our IT infrastructure.',
                'company' => 'Digital Solutions Corp.',
                'location' => 'Davao City',
                'employment_type' => 'Full-time',
                'salary_min' => 25000,
                'salary_max' => 35000,
                'requirements' => 'Bachelor\'s degree in IT or related field, knowledge of Windows/Linux systems, networking basics',
                'benefits' => 'Health insurance, performance bonus, career development',
                'application_deadline' => now()->addDays(45),
                'status' => 'published',
                'is_active' => true,
            ],
        ];

        foreach ($jobPostings as $jobData) {
            JobPosting::updateOrCreate(
                [
                    'title' => $jobData['title'],
                    'company' => $jobData['company'],
                ],
                [
                    'posted_by' => $staff->id,
                    'description' => $jobData['description'],
                    'location' => $jobData['location'],
                    'employment_type' => $jobData['employment_type'],
                    'salary_min' => $jobData['salary_min'],
                    'salary_max' => $jobData['salary_max'],
                    'requirements' => $jobData['requirements'],
                    'benefits' => $jobData['benefits'],
                    'application_deadline' => $jobData['application_deadline'],
                    'status' => $jobData['status'],
                    'is_active' => $jobData['is_active'],
                ]
            );
        }
    }
}