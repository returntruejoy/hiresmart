<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ApplicationResource;
use App\Models\JobPost;
use App\Services\ApplicationService;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Gate;

class ApplicationController extends Controller
{
    use ApiResponseTrait;

    protected ApplicationService $applicationService;

    public function __construct(ApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    public function index(JobPost $jobPost)
    {
        Gate::authorize('view-applications', $jobPost);
        $applications = $this->applicationService->getApplicationsForJobPost($jobPost->id);

        return $this->successResponse(
            ApplicationResource::collection($applications),
            'Applications retrieved successfully.'
        );
    }

    public function store(JobPost $jobPost)
    {
        $application = $this->applicationService->createApplication($jobPost->id);

        return $this->createdResponse(
            new ApplicationResource($application),
            'Application submitted successfully.'
        );
    }
}
