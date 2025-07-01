<?php

namespace App\Services;

use App\Repositories\ApplicationRepository;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ApplicationService
{
    /**
     * @var ApplicationRepository
     */
    protected $applicationRepository;

    /**
     * ApplicationService constructor.
     *
     * @param ApplicationRepository $applicationRepository
     */
    public function __construct(ApplicationRepository $applicationRepository)
    {
        $this->applicationRepository = $applicationRepository;
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

        return $this->applicationRepository->create($data);
    }
} 