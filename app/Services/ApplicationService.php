<?php

namespace App\Services;

use App\Repositories\ApplicationRepository;
use App\Models\Application;
use App\Models\JobPost;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Services\DashboardService;

class ApplicationService
{
    /**
     * @var ApplicationRepository
     */
    protected $applicationRepository;

    /**
     * @var DashboardService
     */
    protected $dashboardService;

    /**
     * ApplicationService constructor.
     *
     * @param ApplicationRepository $applicationRepository
     * @param DashboardService $dashboardService
     */
    public function __construct(
        ApplicationRepository $applicationRepository,
        DashboardService $dashboardService
    ) {
        $this->applicationRepository = $applicationRepository;
        $this->dashboardService = $dashboardService;
    }

    public function getApplicationsForJobPost($jobPostId)
    {
        return $this->applicationRepository->getForJobPost($jobPostId);
    }

    public function createApplication($jobPostId)
    {
        $candidateId = Auth::id();

        $existingApplication = $this->applicationRepository->hasAlreadyApplied($jobPostId, $candidateId);

        if ($existingApplication) {
            throw ValidationException::withMessages([
                'job_post_id' => 'You have already applied for this job.'
            ]);
        }

        $data = [
            'job_post_id' => $jobPostId,
            'candidate_id' => $candidateId,
            'status' => Application::STATUS_SUBMITTED,
        ];

        $application = $this->applicationRepository->create($data);

        // Invalidate employer stats cache
        $jobPost = JobPost::find($jobPostId);
        if ($jobPost) {
            $this->dashboardService->clearEmployerStatsCache($jobPost->employer_id);
        }

        return $application;
    }
} 