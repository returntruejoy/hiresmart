<?php

namespace App\Services;

use App\Repositories\ApplicationRepository;
use App\Repositories\JobPostRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    protected $userRepository;

    protected $jobPostRepository;

    protected $applicationRepository;

    public function __construct(
        UserRepository $userRepository,
        JobPostRepository $jobPostRepository,
        ApplicationRepository $applicationRepository
    ) {
        $this->userRepository = $userRepository;
        $this->jobPostRepository = $jobPostRepository;
        $this->applicationRepository = $applicationRepository;
    }

    public function getMetrics(): array
    {
        return [
            'users' => $this->userRepository->count(),
            'jobs' => $this->jobPostRepository->count(),
            'applications' => $this->applicationRepository->count(),
        ];
    }

    public function getEmployerStats(int $employerId): array
    {
        $cacheKey = "employer_stats_{$employerId}";
        $cacheTtl = 300;

        return Cache::remember($cacheKey, $cacheTtl, function () use ($employerId) {
            return [
                'total_applications' => $this->applicationRepository->countForEmployer($employerId),
                'total_job_posts' => $this->jobPostRepository->getForEmployer($employerId)->count(),
            ];
        });
    }

    public function clearEmployerStatsCache(int $employerId): void
    {
        $cacheKey = "employer_stats_{$employerId}";
        Cache::forget($cacheKey);
    }
}
