<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\JobMatch;
use App\Models\JobPost;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComprehensiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting comprehensive seeding...');

        // Ensure skills exist first
        $this->call(SkillSeeder::class);

        // Get all skills for assignment
        $allSkills = Skill::all();
        $phpSkills = $allSkills->whereIn('name', ['PHP', 'Laravel', 'MySQL', 'JavaScript', 'Git'])->pluck('id');
        $reactSkills = $allSkills->whereIn('name', ['React', 'JavaScript', 'HTML5', 'CSS3', 'Node.js'])->pluck('id');
        $pythonSkills = $allSkills->whereIn('name', ['Python', 'Django', 'PostgreSQL', 'Machine Learning'])->pluck('id');
        $devopsSkills = $allSkills->whereIn('name', ['AWS', 'Docker', 'Kubernetes', 'Jenkins', 'Linux'])->pluck('id');

        // Create employers
        $this->command->info('Creating employers...');
        $employers = User::factory(15)->employer()->create();

        // Create candidates with different skill sets
        $this->command->info('Creating candidates...');

        // Senior PHP/Laravel developers
        $seniorPhpCandidates = User::factory(20)
            ->candidate()
            ->senior()
            ->withLocation('New York, NY')
            ->create();

        foreach ($seniorPhpCandidates as $candidate) {
            $candidate->skills()->attach($phpSkills->random(4));
        }

        // Junior React developers
        $juniorReactCandidates = User::factory(15)
            ->candidate()
            ->junior()
            ->withLocation('San Francisco, CA')
            ->create();

        foreach ($juniorReactCandidates as $candidate) {
            $candidate->skills()->attach($reactSkills->random(3));
        }

        // Python/Data candidates
        $pythonCandidates = User::factory(12)
            ->candidate()
            ->withSalaryExpectation(70000, 120000)
            ->withLocation('Austin, TX')
            ->create();

        foreach ($pythonCandidates as $candidate) {
            $candidate->skills()->attach($pythonSkills->random(3));
        }

        // DevOps candidates
        $devopsCandidates = User::factory(10)
            ->candidate()
            ->senior()
            ->withLocation('Seattle, WA')
            ->create();

        foreach ($devopsCandidates as $candidate) {
            $candidate->skills()->attach($devopsSkills->random(4));
        }

        // Mixed skill candidates
        $mixedCandidates = User::factory(25)
            ->candidate()
            ->create();

        foreach ($mixedCandidates as $candidate) {
            $randomSkills = $allSkills->random(rand(3, 8))->pluck('id');
            $candidate->skills()->attach($randomSkills);
        }

        // Create job posts
        $this->command->info('Creating job posts...');

        $jobPosts = collect();

        foreach ($employers as $employer) {
            // Each employer creates 1-4 job posts
            $jobCount = rand(1, 4);

            for ($i = 0; $i < $jobCount; $i++) {
                $jobPost = JobPost::factory()
                    ->forEmployer($employer)
                    ->create();

                // Assign skills to job posts
                $jobSkills = $allSkills->random(rand(3, 6))->pluck('id');
                $jobPost->skills()->attach($jobSkills);

                $jobPosts->push($jobPost);
            }
        }

        // Create some specific high-quality job posts
        $this->command->info('Creating premium job posts...');

        // Senior Laravel position
        $seniorLaravelJob = JobPost::factory()
            ->forEmployer($employers->random())
            ->senior()
            ->inLocation('New York, NY')
            ->create([
                'title' => 'Senior Laravel Developer',
                'company_name' => 'TechCorp Elite',
                'description' => 'We are seeking a Senior Laravel Developer to join our growing team...',
            ]);
        $seniorLaravelJob->skills()->attach($phpSkills);

        // Frontend React position
        $reactJob = JobPost::factory()
            ->forEmployer($employers->random())
            ->inLocation('San Francisco, CA')
            ->create([
                'title' => 'Frontend React Developer',
                'company_name' => 'InnovateUI',
                'salary_min' => 80000,
                'salary_max' => 120000,
            ]);
        $reactJob->skills()->attach($reactSkills);

        // Remote DevOps position
        $devopsJob = JobPost::factory()
            ->forEmployer($employers->random())
            ->remote()
            ->senior()
            ->create([
                'title' => 'DevOps Engineer',
                'company_name' => 'CloudScale Solutions',
            ]);
        $devopsJob->skills()->attach($devopsSkills);

        // Create applications
        $this->command->info('Creating job applications...');

        $allCandidates = User::where('role', User::ROLE_CANDIDATE)->get();
        $allJobPosts = JobPost::all();

        // Create realistic applications (not every candidate applies to every job)
        foreach ($allCandidates as $candidate) {
            // Each candidate applies to 1-5 jobs
            $applicationCount = rand(1, 5);
            $jobsToApplyTo = $allJobPosts->random($applicationCount);

            foreach ($jobsToApplyTo as $jobPost) {
                // Check if application already exists to avoid duplicates
                if (! Application::where('candidate_id', $candidate->id)
                    ->where('job_post_id', $jobPost->id)
                    ->exists()) {

                    Application::factory()
                        ->forJobPost($jobPost)
                        ->fromCandidate($candidate)
                        ->create();
                }
            }
        }

        // Create job matches
        $this->command->info('Creating job matches...');

        foreach ($allJobPosts as $jobPost) {
            // Each job gets matches with 3-8 candidates
            $matchCount = rand(3, 8);
            $candidatesToMatch = $allCandidates->random($matchCount);

            foreach ($candidatesToMatch as $candidate) {
                // Check if match already exists
                if (! JobMatch::where('candidate_id', $candidate->id)
                    ->where('job_post_id', $jobPost->id)
                    ->exists()) {

                    // Create matches with varying scores
                    $matchType = rand(1, 10);
                    if ($matchType <= 2) {
                        // 20% perfect matches
                        JobMatch::factory()
                            ->perfect()
                            ->forJobPost($jobPost)
                            ->forCandidate($candidate)
                            ->create();
                    } elseif ($matchType <= 5) {
                        // 30% high matches
                        JobMatch::factory()
                            ->highMatch()
                            ->forJobPost($jobPost)
                            ->forCandidate($candidate)
                            ->create();
                    } elseif ($matchType <= 8) {
                        // 30% medium matches
                        JobMatch::factory()
                            ->mediumMatch()
                            ->forJobPost($jobPost)
                            ->forCandidate($candidate)
                            ->create();
                    } else {
                        // 20% low matches
                        JobMatch::factory()
                            ->lowMatch()
                            ->forJobPost($jobPost)
                            ->forCandidate($candidate)
                            ->create();
                    }
                }
            }
        }

        // Create some additional random data
        $this->command->info('Creating additional random data...');

        // Add more diverse applications
        Application::factory(50)->create();

        // Add more job matches
        JobMatch::factory(30)->create();

        $this->command->info('Comprehensive seeding completed!');
        $this->printSummary();
    }

    /**
     * Print a summary of created data.
     */
    private function printSummary(): void
    {
        $this->command->info('=== SEEDING SUMMARY ===');
        $this->command->info('Users: '.User::count());
        $this->command->info('- Admins: '.User::where('role', User::ROLE_ADMIN)->count());
        $this->command->info('- Employers: '.User::where('role', User::ROLE_EMPLOYER)->count());
        $this->command->info('- Candidates: '.User::where('role', User::ROLE_CANDIDATE)->count());
        $this->command->info('Job Posts: '.JobPost::count());
        $this->command->info('Skills: '.Skill::count());
        $this->command->info('Applications: '.Application::count());
        $this->command->info('Job Matches: '.JobMatch::count());
        $this->command->info('User-Skill Relations: '.DB::table('user_skill')->count());
        $this->command->info('JobPost-Skill Relations: '.DB::table('job_post_skill')->count());
        $this->command->info('======================');
    }
}
