<?php

namespace Database\Seeders;

use App\Models\JobPost;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;

class MatchingTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Skills
        $skills = collect(['PHP', 'Laravel', 'Vue.js', 'React', 'PostgreSQL', 'Docker', 'AWS', 'JavaScript'])
            ->map(fn ($name) => Skill::create(['name' => $name]));

        // 2. Create an Employer
        $employer = User::factory()->create([
            'name' => 'Tech Company Inc.',
            'email' => 'employer@example.com',
            'role' => 'employer',
        ]);

        // 3. Create Job Posts
        JobPost::create([
            'employer_id' => $employer->id,
            'title' => 'Senior Laravel Developer',
            'description' => 'Looking for a senior backend developer.',
            'company_name' => 'Tech Company Inc.',
            'location' => 'New York, NY',
            'salary_min' => 100000,
            'salary_max' => 150000,
            'is_active' => true,
        ])->skills()->attach($skills->whereIn('name', ['PHP', 'Laravel', 'PostgreSQL', 'Docker'])->pluck('id'));

        JobPost::create([
            'employer_id' => $employer->id,
            'title' => 'Frontend Developer',
            'description' => 'Building beautiful UIs with Vue.',
            'company_name' => 'Tech Company Inc.',
            'location' => 'Remote',
            'salary_min' => 80000,
            'salary_max' => 120000,
            'is_active' => true,
        ])->skills()->attach($skills->whereIn('name', ['JavaScript', 'Vue.js', 'React'])->pluck('id'));

        // 4. Create Candidates
        $candidate1 = User::factory()->create([
            'name' => 'Alice PerfectMatch',
            'email' => 'alice@example.com',
            'role' => 'candidate',
            'location_preference' => 'New York, NY',
            'salary_expectation_min' => 110000,
            'salary_expectation_max' => 140000,
        ]);
        $candidate1->skills()->attach($skills->whereIn('name', ['PHP', 'Laravel', 'PostgreSQL', 'Docker', 'AWS'])->pluck('id'));

        $candidate2 = User::factory()->create([
            'name' => 'Bob GoodMatch',
            'email' => 'bob@example.com',
            'role' => 'candidate',
            'location_preference' => 'New York, NY',
            'salary_expectation_min' => 155000, // Slightly too high
            'salary_expectation_max' => 170000,
        ]);
        $candidate2->skills()->attach($skills->whereIn('name', ['PHP', 'Laravel'])->pluck('id'));

        $candidate3 = User::factory()->create([
            'name' => 'Charlie Remote',
            'email' => 'charlie@example.com',
            'role' => 'candidate',
            'location_preference' => 'Remote',
            'salary_expectation_min' => 90000,
            'salary_expectation_max' => 110000,
        ]);
        $candidate3->skills()->attach($skills->whereIn('name', ['JavaScript', 'Vue.js'])->pluck('id'));

        $candidate4 = User::factory()->create([
            'name' => 'David WrongLocation',
            'email' => 'david@example.com',
            'role' => 'candidate',
            'location_preference' => 'Boston, MA',
            'salary_expectation_min' => 100000,
            'salary_expectation_max' => 130000,
        ]);
        $candidate4->skills()->attach($skills->whereIn('name', ['PHP', 'Laravel', 'PostgreSQL'])->pluck('id'));
    }
}
