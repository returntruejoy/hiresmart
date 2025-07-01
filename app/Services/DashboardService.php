<?php

namespace App\Services;

use App\Repositories\ApplicationRepository;
use App\Repositories\JobPostRepository;
use App\Repositories\UserRepository;

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
} 