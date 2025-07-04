<?php

namespace App\Services;

use App\Models\JobMatch;
use App\Models\JobPost;
use App\Models\User;
use App\Notifications\CandidateHighMatchNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class JobMatchingService
{
    const MATCH_THRESHOLD = 70;

    public function matchJobsToCandidates()
    {
        $activeJobs = JobPost::where('is_active', true)->with('skills')->get();
        $candidates = User::where('role', 'candidate')->with('skills')->get();

        if ($activeJobs->isEmpty() || $candidates->isEmpty()) {
            Log::info('Job Matching: No active jobs or candidates to match.');

            return;
        }

        foreach ($activeJobs as $job) {
            foreach ($candidates as $candidate) {
                $this->processMatch($job, $candidate);
            }
        }
    }

    private function processMatch(JobPost $job, User $candidate): void
    {
        $scoreResult = $this->calculateMatchScore($job, $candidate);

        $details = [
            'scores' => [
                'skills' => $scoreResult['skills_score'],
                'salary' => $scoreResult['salary_score'],
                'location' => $scoreResult['location_score'],
            ],
            'weights_used' => $scoreResult['weights'],
            'algorithm_version' => '1.1',
        ];

        $match = JobMatch::updateOrCreate(
            [
                'job_post_id' => $job->id,
                'candidate_id' => $candidate->id,
            ],
            [
                'match_score' => $scoreResult['total_score'],
                'match_details' => $details,
                'status' => 'pending',
            ]
        );

        if ($scoreResult['total_score'] >= self::MATCH_THRESHOLD) {
            $candidate->notify(new CandidateHighMatchNotification($match));
            Log::info("High match notification queued for Job #{$job->id} and Candidate #{$candidate->id}. Score: {$scoreResult['total_score']}");
        }
    }

    private function calculateMatchScore(JobPost $job, User $candidate): array
    {
        $weights = config('matching.weights', [
            'skills' => 0.5,
            'salary' => 0.3,
            'location' => 0.2,
        ]);

        $skillsScore = $this->calculateSkillsScore($job->skills, $candidate->skills);
        $salaryScore = $this->calculateSalaryScore($job, $candidate);
        $locationScore = $this->calculateLocationScore($job, $candidate);

        $totalScore = ($skillsScore * $weights['skills'])
                    + ($salaryScore * $weights['salary'])
                    + ($locationScore * $weights['location']);

        return [
            'skills_score' => round($skillsScore),
            'salary_score' => round($salaryScore),
            'location_score' => round($locationScore),
            'total_score' => round($totalScore),
            'weights' => $weights,
        ];
    }

    private function calculateSkillsScore(Collection $jobSkills, Collection $candidateSkills): float
    {
        if ($jobSkills->isEmpty()) {
            return 0;
        }
        $jobSkillIDs = $jobSkills->pluck('id');
        $candidateSkillIDs = $candidateSkills->pluck('id');
        $commonSkillCount = $jobSkillIDs->intersect($candidateSkillIDs)->count();

        return ($commonSkillCount / $jobSkills->count()) * 100;
    }

    private function calculateSalaryScore(JobPost $job, User $candidate): float
    {
        $jobMin = $job->salary_min;
        $jobMax = $job->salary_max;
        $candidateMin = $candidate->salary_expectation_min;
        $candidateMax = $candidate->salary_expectation_max;

        if (is_null($candidateMin) || is_null($candidateMax) || is_null($jobMin) || is_null($jobMax)) {
            return 50;
        }

        // Check for overlap
        if ($candidateMax < $jobMin || $candidateMin > $jobMax) {
            return 0;
        }

        // Favorable if candidate expectation is within or below job range
        if ($candidateMax <= $jobMax) {
            return 100;
        }

        // Less favorable if candidate max expectation exceeds job max
        $diff = $candidateMax - $jobMax;
        $range = $jobMax * 0.2;
        if ($diff > $range) {
            return 25;
        }

        return 100 - (($diff / $range) * 75);
    }

    private function calculateLocationScore(JobPost $job, User $candidate): float
    {
        return strcasecmp(trim($job->location), trim($candidate->location_preference)) === 0 ? 100 : 0;
    }
}
