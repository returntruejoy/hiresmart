<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\JobMatch;
use App\Models\JobPost;
use App\Models\Skill;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EdgeCaseTestSeeder extends Seeder
{
    /**
     * Run the database seeds for testing edge cases.
     */
    public function run(): void
    {
        $this->command->info('Starting edge case test data seeding...');

        // Ensure we have skills
        if (Skill::count() === 0) {
            $this->call(SkillSeeder::class);
        }

        $this->createBoundaryDateUsers();
        $this->createBoundaryDateJobPosts();
        $this->createEdgeCaseApplications();
        $this->createEdgeCaseJobMatches();
        $this->createSpecialCharacterData();
        $this->createSalaryEdgeCases();

        $this->command->info('Edge case test data seeding completed!');
        $this->printEdgeCaseSummary();
    }

    /**
     * Create users with boundary dates.
     */
    private function createBoundaryDateUsers(): void
    {
        $this->command->info('Creating boundary date users...');

        // Unverified users exactly at the 7-day boundary
        User::factory()->candidate()->unverified()->create([
            'name' => 'Exactly 7 Days Unverified',
            'email' => 'exactly7days@boundary.test',
            'created_at' => Carbon::now()->subDays(7)->startOfDay(),
            'updated_at' => Carbon::now()->subDays(7)->startOfDay(),
        ]);

        // Unverified user just over 7 days (7 days + 1 minute)
        User::factory()->candidate()->unverified()->create([
            'name' => 'Just Over 7 Days Unverified',
            'email' => 'justover7days@boundary.test',
            'created_at' => Carbon::now()->subDays(7)->subMinute(1),
            'updated_at' => Carbon::now()->subDays(7)->subMinute(1),
        ]);

        // Unverified user just under 7 days (6 days + 23 hours + 59 minutes)
        User::factory()->candidate()->unverified()->create([
            'name' => 'Just Under 7 Days Unverified',
            'email' => 'justunder7days@boundary.test',
            'created_at' => Carbon::now()->subDays(7)->addMinute(1),
            'updated_at' => Carbon::now()->subDays(7)->addMinute(1),
        ]);

        // User created exactly at midnight
        User::factory()->candidate()->unverified()->create([
            'name' => 'Midnight Created User',
            'email' => 'midnight@boundary.test',
            'created_at' => Carbon::now()->subDays(10)->startOfDay(),
            'updated_at' => Carbon::now()->subDays(10)->startOfDay(),
        ]);

        // User created at end of day
        User::factory()->candidate()->unverified()->create([
            'name' => 'End of Day Created User',
            'email' => 'endofday@boundary.test',
            'created_at' => Carbon::now()->subDays(10)->endOfDay(),
            'updated_at' => Carbon::now()->subDays(10)->endOfDay(),
        ]);
    }

    /**
     * Create job posts with boundary dates.
     */
    private function createBoundaryDateJobPosts(): void
    {
        $this->command->info('Creating boundary date job posts...');

        $employer = User::factory()->employer()->create([
            'name' => 'Boundary Test Employer',
            'email' => 'boundary@employer.test',
        ]);

        // Job post exactly at 30-day boundary
        JobPost::factory()->create([
            'employer_id' => $employer->id,
            'title' => 'Exactly 30 Days Old Job',
            'company_name' => 'Boundary Test Company',
            'is_active' => true,
            'created_at' => Carbon::now()->subDays(30)->startOfDay(),
            'updated_at' => Carbon::now()->subDays(30)->startOfDay(),
        ]);

        // Job post just over 30 days (30 days + 1 minute)
        JobPost::factory()->create([
            'employer_id' => $employer->id,
            'title' => 'Just Over 30 Days Old Job',
            'company_name' => 'Boundary Test Company',
            'is_active' => true,
            'created_at' => Carbon::now()->subDays(30)->subMinute(1),
            'updated_at' => Carbon::now()->subDays(30)->subMinute(1),
        ]);

        // Job post just under 30 days (29 days + 23 hours + 59 minutes)
        JobPost::factory()->create([
            'employer_id' => $employer->id,
            'title' => 'Just Under 30 Days Old Job',
            'company_name' => 'Boundary Test Company',
            'is_active' => true,
            'created_at' => Carbon::now()->subDays(30)->addMinute(1),
            'updated_at' => Carbon::now()->subDays(30)->addMinute(1),
        ]);

        // Very old job posts (different time periods)
        $oldJobDates = [60, 90, 180, 365, 500];
        foreach ($oldJobDates as $days) {
            JobPost::factory()->create([
                'employer_id' => $employer->id,
                'title' => "Job {$days} Days Old",
                'company_name' => 'Very Old Company',
                'is_active' => true,
                'created_at' => Carbon::now()->subDays($days),
                'updated_at' => Carbon::now()->subDays($days),
            ]);
        }
    }

    /**
     * Create edge case applications.
     */
    private function createEdgeCaseApplications(): void
    {
        $this->command->info('Creating edge case applications...');

        $candidate = User::factory()->candidate()->create([
            'name' => 'Edge Case Candidate',
            'email' => 'edgecase@candidate.test',
        ]);

        $employer = User::factory()->employer()->create([
            'name' => 'Edge Case Employer',
            'email' => 'edgecase@employer.test',
        ]);

        $jobPost = JobPost::factory()->create([
            'employer_id' => $employer->id,
            'title' => 'Edge Case Job Post',
        ]);

        // Application with very long cover letter
        Application::factory()->create([
            'job_post_id' => $jobPost->id,
            'candidate_id' => $candidate->id,
            'cover_letter' => str_repeat('This is a very long cover letter. ', 100).
                             'I am extremely interested in this position and have written '.
                             'an exceptionally long cover letter to demonstrate my enthusiasm. '.
                             str_repeat('Additional content. ', 50),
            'status' => Application::STATUS_SUBMITTED,
        ]);

        // Application with no cover letter
        Application::factory()->create([
            'job_post_id' => JobPost::factory()->create(['employer_id' => $employer->id]),
            'candidate_id' => $candidate->id,
            'cover_letter' => null,
            'status' => Application::STATUS_SUBMITTED,
        ]);

        // Application with special characters in cover letter
        Application::factory()->create([
            'job_post_id' => JobPost::factory()->create(['employer_id' => $employer->id]),
            'candidate_id' => $candidate->id,
            'cover_letter' => 'Cover letter with special chars: àáâãäåæçèéêë ñöøùúûü ÿšžœ €£¥ ©®™ 🚀💻🎯',
            'status' => Application::STATUS_VIEWED,
        ]);

        // Applications with all possible statuses
        $statuses = [
            Application::STATUS_SUBMITTED,
            Application::STATUS_VIEWED,
            Application::STATUS_SHORTLISTED,
            Application::STATUS_REJECTED,
        ];

        foreach ($statuses as $status) {
            Application::factory()->create([
                'job_post_id' => JobPost::factory()->create(['employer_id' => $employer->id]),
                'candidate_id' => User::factory()->candidate()->create(),
                'status' => $status,
            ]);
        }
    }

    /**
     * Create edge case job matches.
     */
    private function createEdgeCaseJobMatches(): void
    {
        $this->command->info('Creating edge case job matches...');

        $candidate = User::factory()->candidate()->create([
            'name' => 'Match Test Candidate',
            'email' => 'match@candidate.test',
        ]);

        $employer = User::factory()->employer()->create([
            'name' => 'Match Test Employer',
            'email' => 'match@employer.test',
        ]);

        $jobPost = JobPost::factory()->create([
            'employer_id' => $employer->id,
            'title' => 'Match Test Job',
        ]);

        // Match with minimum possible score
        JobMatch::factory()->create([
            'job_post_id' => $jobPost->id,
            'candidate_id' => $candidate->id,
            'match_score' => 0,
            'match_details' => [
                'skills_match' => 0,
                'salary_match' => 0,
                'location_match' => 0,
                'overall_score' => 0,
                'matched_skills' => [],
                'missing_skills' => ['PHP', 'Laravel', 'React', 'JavaScript'],
                'notes' => 'No match found - completely incompatible.',
            ],
            'status' => 'rejected',
        ]);

        // Match with maximum possible score
        JobMatch::factory()->create([
            'job_post_id' => JobPost::factory()->create(['employer_id' => $employer->id]),
            'candidate_id' => $candidate->id,
            'match_score' => 100,
            'match_details' => [
                'skills_match' => 100,
                'salary_match' => 100,
                'location_match' => 100,
                'overall_score' => 100,
                'matched_skills' => ['PHP', 'Laravel', 'React', 'JavaScript', 'MySQL'],
                'missing_skills' => [],
                'notes' => 'Perfect match in all categories!',
            ],
            'status' => 'pending',
        ]);

        // Match with complex match details
        JobMatch::factory()->create([
            'job_post_id' => JobPost::factory()->create(['employer_id' => $employer->id]),
            'candidate_id' => $candidate->id,
            'match_score' => 75,
            'match_details' => [
                'skills_match' => 85,
                'salary_match' => 60,
                'location_match' => 90,
                'overall_score' => 75,
                'matched_skills' => ['PHP', 'Laravel', 'JavaScript'],
                'missing_skills' => ['React', 'Vue.js'],
                'additional_info' => [
                    'years_experience' => 5,
                    'preferred_technologies' => ['Docker', 'AWS'],
                    'availability' => 'immediate',
                ],
                'notes' => 'Good match with room for growth in frontend technologies.',
            ],
            'status' => 'viewed',
        ]);

        // Matches with all possible statuses
        $statuses = ['pending', 'viewed', 'rejected'];
        foreach ($statuses as $status) {
            JobMatch::factory()->create([
                'job_post_id' => JobPost::factory()->create(['employer_id' => $employer->id]),
                'candidate_id' => User::factory()->candidate()->create(),
                'status' => $status,
            ]);
        }
    }

    /**
     * Create data with special characters and edge cases.
     */
    private function createSpecialCharacterData(): void
    {
        $this->command->info('Creating special character data...');

        // User with special characters in name
        User::factory()->candidate()->create([
            'name' => 'José María Ñoño-Müller',
            'email' => 'special.chars@test.com',
            'bio' => 'Developer with ñoño expertise in François-style programming. Specializes in Zürich-based solutions.',
        ]);

        // User with very long name
        User::factory()->candidate()->create([
            'name' => 'Wolfeschlegelsteinhausenbergerdorff van der Müller-Schmidt',
            'email' => 'verylongname@test.com',
        ]);

        // User with minimal name
        User::factory()->candidate()->create([
            'name' => 'A',
            'email' => 'minimal@test.com',
        ]);

        // Job post with special characters
        $employer = User::factory()->employer()->create();
        JobPost::factory()->create([
            'employer_id' => $employer->id,
            'title' => 'Développeur Full-Stack (français/español)',
            'company_name' => 'Société Européenne & Co.',
            'description' => 'Nous recherchons un développeur passionné par les technologies modernes. Müssen Sie Deutsch sprechen? ¡Hablamos español también!',
            'location' => 'Zürich, Switzerland / São Paulo, Brazil',
        ]);

        // Skills with special characters
        $specialSkills = [
            'C++',
            'C#',
            '.NET',
            'Objective-C',
            'F#',
            'R&D',
            'UI/UX',
            'A/B Testing',
        ];

        foreach ($specialSkills as $skillName) {
            if (! Skill::where('name', $skillName)->exists()) {
                Skill::create(['name' => $skillName]);
            }
        }
    }

    /**
     * Create salary edge cases.
     */
    private function createSalaryEdgeCases(): void
    {
        $this->command->info('Creating salary edge cases...');

        $employer = User::factory()->employer()->create([
            'name' => 'Salary Edge Case Employer',
            'email' => 'salary@edgecase.test',
        ]);

        // Job with very low salary
        JobPost::factory()->create([
            'employer_id' => $employer->id,
            'title' => 'Intern Position',
            'salary_min' => 1,
            'salary_max' => 1000,
        ]);

        // Job with very high salary
        JobPost::factory()->create([
            'employer_id' => $employer->id,
            'title' => 'CEO Position',
            'salary_min' => 500000,
            'salary_max' => 1000000,
        ]);

        // Job with same min and max salary
        JobPost::factory()->create([
            'employer_id' => $employer->id,
            'title' => 'Fixed Salary Position',
            'salary_min' => 75000,
            'salary_max' => 75000,
        ]);

        // Job with no salary specified
        JobPost::factory()->create([
            'employer_id' => $employer->id,
            'title' => 'Salary TBD Position',
            'salary_min' => null,
            'salary_max' => null,
        ]);

        // Candidates with edge case salary expectations
        User::factory()->candidate()->create([
            'name' => 'Low Salary Expectation Candidate',
            'email' => 'lowsalary@candidate.test',
            'salary_expectation_min' => 1,
            'salary_expectation_max' => 5000,
        ]);

        User::factory()->candidate()->create([
            'name' => 'High Salary Expectation Candidate',
            'email' => 'highsalary@candidate.test',
            'salary_expectation_min' => 300000,
            'salary_expectation_max' => 500000,
        ]);

        User::factory()->candidate()->create([
            'name' => 'No Salary Expectation Candidate',
            'email' => 'nosalary@candidate.test',
            'salary_expectation_min' => null,
            'salary_expectation_max' => null,
        ]);
    }

    /**
     * Print a summary of created edge case data.
     */
    private function printEdgeCaseSummary(): void
    {
        $this->command->info('=== EDGE CASE TEST DATA SUMMARY ===');

        // Boundary users
        $boundaryUsers = User::where('email', 'like', '%boundary.test')->count();
        $this->command->info("Boundary Date Users: {$boundaryUsers}");

        // Boundary job posts
        $boundaryJobs = JobPost::where('company_name', 'Boundary Test Company')->count();
        $this->command->info("Boundary Date Job Posts: {$boundaryJobs}");

        // Edge case applications
        $edgeCaseApps = Application::whereHas('candidate', function ($query) {
            $query->where('email', 'edgecase@candidate.test');
        })->count();
        $this->command->info("Edge Case Applications: {$edgeCaseApps}");

        // Edge case matches
        $edgeCaseMatches = JobMatch::whereIn('match_score', [0, 100])->count();
        $this->command->info("Extreme Score Matches (0 or 100): {$edgeCaseMatches}");

        // Special character data
        $specialCharUsers = User::where('name', 'like', '%ñ%')
            ->orWhere('name', 'like', '%ü%')
            ->orWhere('name', 'like', '%é%')
            ->count();
        $this->command->info("Special Character Users: {$specialCharUsers}");

        // Salary edge cases
        $extremeSalaryJobs = JobPost::where('salary_min', '<', 1000)
            ->orWhere('salary_max', '>', 500000)
            ->orWhereNull('salary_min')
            ->count();
        $this->command->info("Extreme Salary Job Posts: {$extremeSalaryJobs}");

        $this->command->info('');
        $this->command->info('Use this data to test:');
        $this->command->info('- Boundary conditions in cleanup commands');
        $this->command->info('- Special character handling');
        $this->command->info('- Extreme salary matching scenarios');
        $this->command->info('- Application and match edge cases');
        $this->command->info('=====================================');
    }
}
