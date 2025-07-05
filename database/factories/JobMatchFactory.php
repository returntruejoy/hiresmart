<?php

namespace Database\Factories;

use App\Models\JobPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobMatch>
 */
class JobMatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $matchScore = fake()->numberBetween(40, 95);

        return [
            'job_post_id' => JobPost::factory(),
            'candidate_id' => User::factory()->candidate(),
            'match_score' => $matchScore,
            'match_details' => $this->generateMatchDetails($matchScore),
            'status' => fake()->randomElement(['pending', 'viewed', 'rejected']),
        ];
    }

    /**
     * Generate realistic match details based on score.
     */
    private function generateMatchDetails(int $score): array
    {
        $skillsScore = fake()->numberBetween(max(0, $score - 20), min(100, $score + 20));
        $salaryScore = fake()->numberBetween(max(0, $score - 15), min(100, $score + 15));
        $locationScore = fake()->numberBetween(max(0, $score - 25), min(100, $score + 25));

        return [
            'skills_match' => $skillsScore,
            'salary_match' => $salaryScore,
            'location_match' => $locationScore,
            'overall_score' => $score,
            'matched_skills' => fake()->randomElements([
                'PHP', 'Laravel', 'JavaScript', 'React', 'MySQL', 'Git', 'Docker',
            ], fake()->numberBetween(2, 5)),
            'missing_skills' => fake()->randomElements([
                'AWS', 'Kubernetes', 'Vue.js', 'PostgreSQL', 'Redis',
            ], fake()->numberBetween(0, 3)),
            'notes' => $this->generateMatchNotes($score),
        ];
    }

    /**
     * Generate match notes based on score.
     */
    private function generateMatchNotes(int $score): string
    {
        if ($score >= 80) {
            return 'Excellent match! Strong alignment in skills, salary expectations, and location preferences.';
        } elseif ($score >= 70) {
            return 'Very good match with most requirements met. Minor gaps in some areas.';
        } elseif ($score >= 60) {
            return 'Good match with solid foundation. Some skill development may be needed.';
        } else {
            return 'Moderate match. Significant gaps in requirements or preferences.';
        }
    }

    /**
     * Create a high-scoring match (80-95).
     */
    public function highMatch(): static
    {
        $score = fake()->numberBetween(80, 95);

        return $this->state(fn (array $attributes) => [
            'match_score' => $score,
            'match_details' => $this->generateMatchDetails($score),
            'status' => 'pending',
        ]);
    }

    /**
     * Create a medium-scoring match (60-79).
     */
    public function mediumMatch(): static
    {
        $score = fake()->numberBetween(60, 79);

        return $this->state(fn (array $attributes) => [
            'match_score' => $score,
            'match_details' => $this->generateMatchDetails($score),
        ]);
    }

    /**
     * Create a low-scoring match (40-59).
     */
    public function lowMatch(): static
    {
        $score = fake()->numberBetween(40, 59);

        return $this->state(fn (array $attributes) => [
            'match_score' => $score,
            'match_details' => $this->generateMatchDetails($score),
        ]);
    }

    /**
     * Create a match with pending status.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Create a match with viewed status.
     */
    public function viewed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'viewed',
        ]);
    }

    /**
     * Create a match with rejected status.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
        ]);
    }

    /**
     * Create a match for a specific job post.
     */
    public function forJobPost(JobPost $jobPost): static
    {
        return $this->state(fn (array $attributes) => [
            'job_post_id' => $jobPost->id,
        ]);
    }

    /**
     * Create a match for a specific candidate.
     */
    public function forCandidate(User $candidate): static
    {
        return $this->state(fn (array $attributes) => [
            'candidate_id' => $candidate->id,
        ]);
    }

    /**
     * Create a perfect match (90-95 score).
     */
    public function perfect(): static
    {
        $score = fake()->numberBetween(90, 95);

        return $this->state(fn (array $attributes) => [
            'match_score' => $score,
            'match_details' => [
                'skills_match' => fake()->numberBetween(85, 100),
                'salary_match' => fake()->numberBetween(90, 100),
                'location_match' => fake()->numberBetween(85, 100),
                'overall_score' => $score,
                'matched_skills' => ['PHP', 'Laravel', 'JavaScript', 'React', 'MySQL', 'Git'],
                'missing_skills' => [],
                'notes' => 'Perfect match! All requirements and preferences align excellently.',
            ],
            'status' => 'pending',
        ]);
    }
}
