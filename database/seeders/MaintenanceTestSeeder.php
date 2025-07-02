<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\JobPost;
use App\Models\Skill;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MaintenanceTestSeeder extends Seeder
{
    /**
     * Run the database seeds for testing maintenance tasks.
     */
    public function run(): void
    {
        $this->command->info('Starting maintenance test data seeding...');

        // Ensure we have skills
        if (Skill::count() === 0) {
            $this->call(SkillSeeder::class);
        }

        $this->createUnverifiedUsers();
        $this->createOldJobPosts();
        $this->createRecentDataForComparison();

        $this->command->info('Maintenance test data seeding completed!');
        $this->printMaintenanceSummary();
    }

    /**
     * Create unverified users of various ages.
     */
    private function createUnverifiedUsers(): void
    {
        $this->command->info('Creating unverified users for cleanup testing...');

        // Users created more than 7 days ago (should be cleaned up)
        $oldUnverifiedUsers = [
            ['days_ago' => 8, 'name' => 'Old Unverified User 1'],
            ['days_ago' => 10, 'name' => 'Old Unverified User 2'],
            ['days_ago' => 15, 'name' => 'Old Unverified User 3'],
            ['days_ago' => 30, 'name' => 'Very Old Unverified User'],
            ['days_ago' => 45, 'name' => 'Ancient Unverified User'],
        ];

        foreach ($oldUnverifiedUsers as $userData) {
            User::factory()
                ->candidate()
                ->unverified()
                ->create([
                    'name' => $userData['name'],
                    'email' => strtolower(str_replace(' ', '', $userData['name'])) . '@example.com',
                    'created_at' => Carbon::now()->subDays($userData['days_ago']),
                    'updated_at' => Carbon::now()->subDays($userData['days_ago']),
                ]);
        }

        // Users created less than 7 days ago (should NOT be cleaned up)
        $recentUnverifiedUsers = [
            ['days_ago' => 1, 'name' => 'Recent Unverified User 1'],
            ['days_ago' => 3, 'name' => 'Recent Unverified User 2'],
            ['days_ago' => 6, 'name' => 'Recent Unverified User 3'],
        ];

        foreach ($recentUnverifiedUsers as $userData) {
            User::factory()
                ->candidate()
                ->unverified()
                ->create([
                    'name' => $userData['name'],
                    'email' => strtolower(str_replace(' ', '', $userData['name'])) . '@example.com',
                    'created_at' => Carbon::now()->subDays($userData['days_ago']),
                    'updated_at' => Carbon::now()->subDays($userData['days_ago']),
                ]);
        }

        // Some verified users that are old (should NOT be cleaned up)
        $oldVerifiedUsers = [
            ['days_ago' => 20, 'name' => 'Old Verified Candidate'],
            ['days_ago' => 60, 'name' => 'Ancient Verified Employer'],
        ];

        foreach ($oldVerifiedUsers as $userData) {
            $role = str_contains($userData['name'], 'Employer') ? 'employer' : 'candidate';
            
            User::factory()
                ->withRole($role)
                ->create([
                    'name' => $userData['name'],
                    'email' => strtolower(str_replace(' ', '', $userData['name'])) . '@example.com',
                    'email_verified_at' => Carbon::now()->subDays($userData['days_ago'] - 1),
                    'created_at' => Carbon::now()->subDays($userData['days_ago']),
                    'updated_at' => Carbon::now()->subDays($userData['days_ago']),
                ]);
        }
    }

    /**
     * Create job posts of various ages.
     */
    private function createOldJobPosts(): void
    {
        $this->command->info('Creating old job posts for archival testing...');

        // Create some employers first
        $oldEmployer = User::factory()->employer()->create([
            'name' => 'Old Company Employer',
            'email' => 'oldemployer@testcompany.com',
            'created_at' => Carbon::now()->subDays(60),
        ]);

        $recentEmployer = User::factory()->employer()->create([
            'name' => 'Recent Company Employer',
            'email' => 'recentemployer@testcompany.com',
            'created_at' => Carbon::now()->subDays(10),
        ]);

        // Job posts created more than 30 days ago (should be archived)
        $oldJobPosts = [
            ['days_ago' => 31, 'title' => 'Old PHP Developer Position'],
            ['days_ago' => 45, 'title' => 'Ancient React Developer Role'],
            ['days_ago' => 60, 'title' => 'Very Old Full Stack Position'],
            ['days_ago' => 90, 'title' => 'Expired Senior Developer Job'],
            ['days_ago' => 120, 'title' => 'Long Expired DevOps Role'],
        ];

        $skills = Skill::inRandomOrder()->take(5)->get();

        foreach ($oldJobPosts as $jobData) {
            $jobPost = JobPost::factory()->create([
                'employer_id' => $oldEmployer->id,
                'title' => $jobData['title'],
                'company_name' => 'Old Tech Company',
                'is_active' => true, // These should be set to false by the archive command
                'created_at' => Carbon::now()->subDays($jobData['days_ago']),
                'updated_at' => Carbon::now()->subDays($jobData['days_ago']),
            ]);

            // Assign some skills
            $jobPost->skills()->attach($skills->random(3)->pluck('id'));

            // Create some applications for these old jobs
            $candidates = User::factory(2)->candidate()->create();
            foreach ($candidates as $candidate) {
                Application::factory()->create([
                    'job_post_id' => $jobPost->id,
                    'candidate_id' => $candidate->id,
                    'created_at' => Carbon::now()->subDays($jobData['days_ago'] - 5),
                ]);
            }
        }

        // Job posts created less than 30 days ago (should NOT be archived)
        $recentJobPosts = [
            ['days_ago' => 1, 'title' => 'Brand New Laravel Developer'],
            ['days_ago' => 7, 'title' => 'Week Old React Position'],
            ['days_ago' => 15, 'title' => 'Two Week Old Full Stack Job'],
            ['days_ago' => 29, 'title' => 'Almost Month Old DevOps Role'],
        ];

        foreach ($recentJobPosts as $jobData) {
            $jobPost = JobPost::factory()->create([
                'employer_id' => $recentEmployer->id,
                'title' => $jobData['title'],
                'company_name' => 'Recent Tech Startup',
                'is_active' => true, // These should remain active
                'created_at' => Carbon::now()->subDays($jobData['days_ago']),
                'updated_at' => Carbon::now()->subDays($jobData['days_ago']),
            ]);

            // Assign some skills
            $jobPost->skills()->attach($skills->random(3)->pluck('id'));
        }

        // Some already inactive job posts (mixed ages)
        $inactiveJobPosts = [
            ['days_ago' => 20, 'title' => 'Already Inactive Recent Job'],
            ['days_ago' => 50, 'title' => 'Already Inactive Old Job'],
        ];

        foreach ($inactiveJobPosts as $jobData) {
            JobPost::factory()->create([
                'employer_id' => $oldEmployer->id,
                'title' => $jobData['title'],
                'company_name' => 'Inactive Job Company',
                'is_active' => false, // Already inactive
                'created_at' => Carbon::now()->subDays($jobData['days_ago']),
                'updated_at' => Carbon::now()->subDays($jobData['days_ago']),
            ]);
        }
    }

    /**
     * Create some recent data for comparison.
     */
    private function createRecentDataForComparison(): void
    {
        $this->command->info('Creating recent data for comparison...');

        // Recent verified users (should never be touched by cleanup)
        User::factory(3)->candidate()->create([
            'created_at' => Carbon::now()->subDays(2),
        ]);

        User::factory(2)->employer()->create([
            'created_at' => Carbon::now()->subDays(1),
        ]);

        // Recent active job posts
        $recentEmployer = User::where('role', 'employer')->latest()->first();
        JobPost::factory(3)->create([
            'employer_id' => $recentEmployer->id,
            'is_active' => true,
            'created_at' => Carbon::now()->subDays(5),
        ]);
    }

    /**
     * Print a summary of created maintenance test data.
     */
    private function printMaintenanceSummary(): void
    {
        $this->command->info('=== MAINTENANCE TEST DATA SUMMARY ===');
        
        // Unverified users summary
        $oldUnverified = User::whereNull('email_verified_at')
            ->where('created_at', '<', Carbon::now()->subDays(7))
            ->count();
        
        $recentUnverified = User::whereNull('email_verified_at')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();

        $this->command->info("Unverified Users:");
        $this->command->info("- Old (>7 days, should be cleaned): {$oldUnverified}");
        $this->command->info("- Recent (≤7 days, should remain): {$recentUnverified}");

        // Job posts summary
        $oldActiveJobs = JobPost::where('is_active', true)
            ->where('created_at', '<', Carbon::now()->subDays(30))
            ->count();
        
        $recentActiveJobs = JobPost::where('is_active', true)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();

        $alreadyInactiveJobs = JobPost::where('is_active', false)->count();

        $this->command->info("Job Posts:");
        $this->command->info("- Old active (>30 days, should be archived): {$oldActiveJobs}");
        $this->command->info("- Recent active (≤30 days, should remain active): {$recentActiveJobs}");
        $this->command->info("- Already inactive: {$alreadyInactiveJobs}");

        $this->command->info('');
        $this->command->info('Test Commands:');
        $this->command->info('php artisan app:remove-unverified-users');
        $this->command->info('php artisan jobs:archive-old');
        $this->command->info('=====================================');
    }
} 