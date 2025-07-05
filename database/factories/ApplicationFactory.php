<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\JobPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $coverLetterTemplates = [
            "Dear Hiring Manager,\n\nI am writing to express my strong interest in this position. With my background in software development and passion for creating innovative solutions, I believe I would be a valuable addition to your team.\n\nI have experience working with modern technologies and am excited about the opportunity to contribute to your company's success.\n\nThank you for considering my application.\n\nBest regards,",

            "Hello,\n\nI am excited to apply for this role as it aligns perfectly with my career goals and technical expertise. My experience in web development and collaborative work environments has prepared me well for this opportunity.\n\nI am particularly drawn to your company's mission and would love to discuss how my skills can contribute to your team's objectives.\n\nLooking forward to hearing from you.",

            "Dear Team,\n\nI am enthusiastic about the opportunity to join your organization. My technical background and problem-solving abilities make me well-suited for this position.\n\nI am eager to bring my skills and fresh perspective to your team and contribute to meaningful projects.\n\nThank you for your time and consideration.",
        ];

        return [
            'job_post_id' => JobPost::factory(),
            'candidate_id' => User::factory()->candidate(),
            'status' => fake()->randomElement([
                Application::STATUS_SUBMITTED,
                Application::STATUS_VIEWED,
                Application::STATUS_SHORTLISTED,
                Application::STATUS_REJECTED,
            ]),
            'cover_letter' => fake()->boolean(70) ? fake()->randomElement($coverLetterTemplates) : null,
        ];
    }

    /**
     * Create an application with submitted status.
     */
    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Application::STATUS_SUBMITTED,
        ]);
    }

    /**
     * Create an application with viewed status.
     */
    public function viewed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Application::STATUS_VIEWED,
        ]);
    }

    /**
     * Create an application with shortlisted status.
     */
    public function shortlisted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Application::STATUS_SHORTLISTED,
        ]);
    }

    /**
     * Create an application with rejected status.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Application::STATUS_REJECTED,
        ]);
    }

    /**
     * Create an application for a specific job post.
     */
    public function forJobPost(JobPost $jobPost): static
    {
        return $this->state(fn (array $attributes) => [
            'job_post_id' => $jobPost->id,
        ]);
    }

    /**
     * Create an application from a specific candidate.
     */
    public function fromCandidate(User $candidate): static
    {
        return $this->state(fn (array $attributes) => [
            'candidate_id' => $candidate->id,
        ]);
    }

    /**
     * Create an application with a cover letter.
     */
    public function withCoverLetter(): static
    {
        $coverLetter = "Dear Hiring Manager,\n\nI am writing to express my genuine interest in this position. After reviewing the job description, I am confident that my skills and experience align well with your requirements.\n\nMy technical expertise and passion for continuous learning make me an ideal candidate for this role. I am excited about the opportunity to contribute to your team's success and grow within your organization.\n\nI would welcome the opportunity to discuss how my background and enthusiasm can benefit your company. Thank you for considering my application.\n\nSincerely,";

        return $this->state(fn (array $attributes) => [
            'cover_letter' => $coverLetter,
        ]);
    }

    /**
     * Create an application without a cover letter.
     */
    public function withoutCoverLetter(): static
    {
        return $this->state(fn (array $attributes) => [
            'cover_letter' => null,
        ]);
    }
}
