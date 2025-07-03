<?php

namespace Database\Factories;

use App\Models\JobPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobPost>
 */
class JobPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jobTitles = [
            'Senior Laravel Developer',
            'Frontend React Developer',
            'Full Stack Developer',
            'DevOps Engineer',
            'Backend PHP Developer',
            'Mobile App Developer',
            'UI/UX Designer',
            'Data Analyst',
            'Product Manager',
            'QA Engineer',
            'Python Developer',
            'JavaScript Developer',
            'Software Architect',
            'Technical Lead',
            'Junior Web Developer'
        ];

        $companies = [
            'TechCorp Solutions',
            'InnovateNow Inc',
            'Digital Dynamics',
            'CodeCraft Studios',
            'NextGen Technologies',
            'Pixel Perfect Design',
            'DataDriven Analytics',
            'CloudFirst Solutions',
            'Agile Innovations',
            'StartupHub Ventures'
        ];

        $locations = [
            'New York, NY',
            'San Francisco, CA',
            'Austin, TX',
            'Seattle, WA',
            'Chicago, IL',
            'Boston, MA',
            'Denver, CO',
            'Los Angeles, CA',
            'Miami, FL',
            'Remote'
        ];

        $salaryMin = fake()->numberBetween(50000, 120000);
        $salaryMax = $salaryMin + fake()->numberBetween(20000, 80000);

        return [
            'employer_id' => User::factory()->employer(),
            'title' => fake()->randomElement($jobTitles),
            'description' => $this->generateJobDescription(),
            'company_name' => fake()->randomElement($companies),
            'company_description' => fake()->paragraph(3),
            'location' => fake()->randomElement($locations),
            'salary_min' => $salaryMin,
            'salary_max' => $salaryMax,
            'is_active' => fake()->boolean(85),
    }

    /**
     * Generate a realistic job description.
     */
    private function generateJobDescription(): string
    {
        $responsibilities = [
            'Develop and maintain web applications using modern frameworks',
            'Collaborate with cross-functional teams to define and implement new features',
            'Write clean, maintainable, and efficient code',
            'Participate in code reviews and ensure code quality standards',
            'Troubleshoot and debug applications',
            'Optimize applications for maximum speed and scalability',
            'Stay up-to-date with emerging technologies and industry trends',
            'Mentor junior developers and provide technical guidance'
        ];

        $requirements = [
            'Bachelor\'s degree in Computer Science or related field',
            'Strong problem-solving and analytical skills',
            'Excellent communication and teamwork abilities',
            'Experience with version control systems (Git)',
            'Knowledge of database design and SQL',
            'Understanding of software development lifecycle',
            'Ability to work in an agile development environment'
        ];

        $selectedResponsibilities = fake()->randomElements($responsibilities, fake()->numberBetween(4, 6));
        $selectedRequirements = fake()->randomElements($requirements, fake()->numberBetween(3, 5));

        return "**Responsibilities:**\n" . 
               implode("\n", array_map(fn($item) => "• $item", $selectedResponsibilities)) . 
               "\n\n**Requirements:**\n" . 
               implode("\n", array_map(fn($item) => "• $item", $selectedRequirements));
    }

    /**
     * Create an active job post.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Create an inactive job post.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create a job post for a specific employer.
     */
    public function forEmployer(User $employer): static
    {
        return $this->state(fn (array $attributes) => [
            'employer_id' => $employer->id,
        ]);
    }

    /**
     * Create a job post in a specific location.
     */
    public function inLocation(string $location): static
    {
        return $this->state(fn (array $attributes) => [
            'location' => $location,
        ]);
    }

    /**
     * Create a job post with specific salary range.
     */
    public function withSalary(int $min, int $max): static
    {
        return $this->state(fn (array $attributes) => [
            'salary_min' => $min,
            'salary_max' => $max,
        ]);
    }

    /**
     * Create a senior-level job post with higher salary.
     */
    public function senior(): static
    {
        $seniorTitles = [
            'Senior Software Engineer',
            'Lead Developer',
            'Principal Engineer',
            'Software Architect',
            'Technical Lead',
            'Senior Full Stack Developer'
        ];

        return $this->state(fn (array $attributes) => [
            'title' => fake()->randomElement($seniorTitles),
            'salary_min' => fake()->numberBetween(90000, 140000),
            'salary_max' => fake()->numberBetween(150000, 220000),
        ]);
    }

    /**
     * Create a junior-level job post with lower salary.
     */
    public function junior(): static
    {
        $juniorTitles = [
            'Junior Developer',
            'Entry Level Software Engineer',
            'Associate Developer',
            'Junior Web Developer',
            'Graduate Software Developer'
        ];

        return $this->state(fn (array $attributes) => [
            'title' => fake()->randomElement($juniorTitles),
            'salary_min' => fake()->numberBetween(35000, 55000),
            'salary_max' => fake()->numberBetween(60000, 80000),
        ]);
    }

    /**
     * Create a remote job post.
     */
    public function remote(): static
    {
        return $this->state(fn (array $attributes) => [
            'location' => 'Remote',
        ]);
    }
} 