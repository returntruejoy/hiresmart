<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\JobMatch;
use App\Models\JobPost;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds for development environment.
     */
    public function run(): void
    {
        $this->command->info('Starting development seeding...');

        // Seed basic skills first
        $this->call(SkillSeeder::class);

        // Create a few key skills for testing
        $phpSkill = Skill::where('name', 'PHP')->first();
        $laravelSkill = Skill::where('name', 'Laravel')->first();
        $reactSkill = Skill::where('name', 'React')->first();
        $jsSkill = Skill::where('name', 'JavaScript')->first();
        $mysqlSkill = Skill::where('name', 'MySQL')->first();

        // Create test employers
        $this->command->info('Creating test employers...');
        $employer1 = User::factory()->employer()->create([
            'name' => 'John Employer',
            'email' => 'employer1@hiresmart.com',
            'bio' => 'Tech company founder with 10 years of experience.',
        ]);

        $employer2 = User::factory()->employer()->create([
            'name' => 'Sarah Manager',
            'email' => 'employer2@hiresmart.com',
            'bio' => 'HR Manager at a growing startup.',
        ]);

        // Create test candidates
        $this->command->info('Creating test candidates...');
        
        // Senior PHP/Laravel candidate
        $candidate1 = User::factory()->candidate()->create([
            'name' => 'Alice Developer',
            'email' => 'candidate1@hiresmart.com',
            'location_preference' => 'New York, NY',
            'salary_expectation_min' => 90000,
            'salary_expectation_max' => 130000,
            'bio' => 'Senior Laravel developer with 7 years of experience building scalable web applications.',
        ]);
        $candidate1->skills()->attach([$phpSkill->id, $laravelSkill->id, $jsSkill->id, $mysqlSkill->id]);

        // Junior React candidate
        $candidate2 = User::factory()->candidate()->create([
            'name' => 'Bob Frontend',
            'email' => 'candidate2@hiresmart.com',
            'location_preference' => 'San Francisco, CA',
            'salary_expectation_min' => 60000,
            'salary_expectation_max' => 85000,
            'bio' => 'Junior frontend developer passionate about React and modern web technologies.',
        ]);
        $candidate2->skills()->attach([$reactSkill->id, $jsSkill->id]);

        // Full-stack candidate
        $candidate3 = User::factory()->candidate()->create([
            'name' => 'Carol Fullstack',
            'email' => 'candidate3@hiresmart.com',
            'location_preference' => 'Remote',
            'salary_expectation_min' => 80000,
            'salary_expectation_max' => 120000,
            'bio' => 'Full-stack developer comfortable with both backend and frontend technologies.',
        ]);
        $candidate3->skills()->attach([$phpSkill->id, $laravelSkill->id, $reactSkill->id, $jsSkill->id, $mysqlSkill->id]);

        // Create test job posts
        $this->command->info('Creating test job posts...');
        
        // Senior Laravel position
        $job1 = JobPost::factory()->create([
            'employer_id' => $employer1->id,
            'title' => 'Senior Laravel Developer',
            'description' => 'We are looking for a senior Laravel developer to join our team and lead exciting projects.',
            'company_name' => 'TechCorp Solutions',
            'company_description' => 'A leading technology company specializing in web applications.',
            'location' => 'New York, NY',
            'salary_min' => 85000,
            'salary_max' => 125000,
            'is_active' => true,
        ]);
        $job1->skills()->attach([$phpSkill->id, $laravelSkill->id, $mysqlSkill->id]);

        // Frontend React position
        $job2 = JobPost::factory()->create([
            'employer_id' => $employer2->id,
            'title' => 'Frontend React Developer',
            'description' => 'Join our team as a React developer and help build amazing user interfaces.',
            'company_name' => 'InnovateUI',
            'company_description' => 'A design-focused startup creating beautiful web experiences.',
            'location' => 'San Francisco, CA',
            'salary_min' => 70000,
            'salary_max' => 95000,
            'is_active' => true,
        ]);
        $job2->skills()->attach([$reactSkill->id, $jsSkill->id]);

        // Full-stack remote position
        $job3 = JobPost::factory()->create([
            'employer_id' => $employer1->id,
            'title' => 'Full Stack Developer (Remote)',
            'description' => 'Remote opportunity for a full-stack developer to work on diverse projects.',
            'company_name' => 'TechCorp Solutions',
            'company_description' => 'A leading technology company specializing in web applications.',
            'location' => 'Remote',
            'salary_min' => 75000,
            'salary_max' => 110000,
            'is_active' => true,
        ]);
        $job3->skills()->attach([$phpSkill->id, $laravelSkill->id, $reactSkill->id, $jsSkill->id]);

        // Create test applications
        $this->command->info('Creating test applications...');
        
        // Alice applies to Senior Laravel position (perfect match)
        Application::factory()->create([
            'job_post_id' => $job1->id,
            'candidate_id' => $candidate1->id,
            'status' => Application::STATUS_SUBMITTED,
            'cover_letter' => 'Dear Hiring Manager, I am very interested in the Senior Laravel Developer position. My 7 years of experience with Laravel and PHP make me an ideal candidate for this role.',
        ]);

        // Bob applies to React position (good match)
        Application::factory()->create([
            'job_post_id' => $job2->id,
            'candidate_id' => $candidate2->id,
            'status' => Application::STATUS_VIEWED,
            'cover_letter' => 'Hello, I am excited about the Frontend React Developer opportunity. As a passionate React developer, I would love to contribute to your team.',
        ]);

        // Carol applies to full-stack position (excellent match)
        Application::factory()->create([
            'job_post_id' => $job3->id,
            'candidate_id' => $candidate3->id,
            'status' => Application::STATUS_SHORTLISTED,
            'cover_letter' => 'Hi there, The Full Stack Developer position aligns perfectly with my skills and career goals. I have experience with both Laravel and React.',
        ]);

        // Create test job matches
        $this->command->info('Creating test job matches...');
        
        // High match: Alice + Senior Laravel job
        JobMatch::factory()->create([
            'job_post_id' => $job1->id,
            'candidate_id' => $candidate1->id,
            'match_score' => 92,
            'match_details' => [
                'skills_match' => 95,
                'salary_match' => 88,
                'location_match' => 100,
                'overall_score' => 92,
                'matched_skills' => ['PHP', 'Laravel', 'MySQL'],
                'missing_skills' => [],
                'notes' => 'Excellent match! Perfect skill alignment and location preference.',
            ],
            'status' => 'pending',
        ]);

        // Medium match: Bob + React job
        JobMatch::factory()->create([
            'job_post_id' => $job2->id,
            'candidate_id' => $candidate2->id,
            'match_score' => 78,
            'match_details' => [
                'skills_match' => 85,
                'salary_match' => 75,
                'location_match' => 100,
                'overall_score' => 78,
                'matched_skills' => ['React', 'JavaScript'],
                'missing_skills' => ['HTML5', 'CSS3'],
                'notes' => 'Good match with strong React skills. Some frontend skills could be improved.',
            ],
            'status' => 'pending',
        ]);

        // Perfect match: Carol + Full-stack job
        JobMatch::factory()->create([
            'job_post_id' => $job3->id,
            'candidate_id' => $candidate3->id,
            'match_score' => 94,
            'match_details' => [
                'skills_match' => 100,
                'salary_match' => 90,
                'location_match' => 100,
                'overall_score' => 94,
                'matched_skills' => ['PHP', 'Laravel', 'React', 'JavaScript', 'MySQL'],
                'missing_skills' => [],
                'notes' => 'Perfect match! Full-stack skills align perfectly with requirements.',
            ],
            'status' => 'pending',
        ]);

        // Add some additional random data for variety
        $this->command->info('Adding additional test data...');
        
        // Create a few more candidates and employers
        User::factory(5)->employer()->create();
        User::factory(10)->candidate()->create()->each(function ($candidate) {
            $skills = Skill::inRandomOrder()->take(rand(2, 6))->pluck('id');
            $candidate->skills()->attach($skills);
        });

        // Create more job posts
        JobPost::factory(8)->create()->each(function ($jobPost) {
            $skills = Skill::inRandomOrder()->take(rand(3, 5))->pluck('id');
            $jobPost->skills()->attach($skills);
        });

        // Create more applications and matches
        Application::factory(15)->create();
        JobMatch::factory(20)->create();

        $this->command->info('Development seeding completed!');
        $this->printDevelopmentSummary();
    }

    /**
     * Print a summary of created development data.
     */
    private function printDevelopmentSummary(): void
    {
        $this->command->info('=== DEVELOPMENT SEEDING SUMMARY ===');
        $this->command->info('Test Accounts Created:');
        $this->command->info('- employer1@hiresmart.com (John Employer)');
        $this->command->info('- employer2@hiresmart.com (Sarah Manager)');
        $this->command->info('- candidate1@hiresmart.com (Alice Developer - Senior Laravel)');
        $this->command->info('- candidate2@hiresmart.com (Bob Frontend - Junior React)');
        $this->command->info('- candidate3@hiresmart.com (Carol Fullstack - Full Stack)');
        $this->command->info('');
        $this->command->info('Total Data:');
        $this->command->info('- Users: ' . User::count());
        $this->command->info('- Job Posts: ' . JobPost::count());
        $this->command->info('- Skills: ' . Skill::count());
        $this->command->info('- Applications: ' . Application::count());
        $this->command->info('- Job Matches: ' . JobMatch::count());
        $this->command->info('');
        $this->command->info('All test accounts use password: "password"');
        $this->command->info('===================================');
    }
} 