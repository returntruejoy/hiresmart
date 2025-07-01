<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ApplicationService;
use App\Http\Resources\Api\V1\ApplicationResource;
use App\Models\JobPost;
use Illuminate\Support\Facades\Gate;

class ApplicationController extends Controller
{
    /**
     * @var ApplicationService
     */
    protected $applicationService;

    /**
     * ApplicationController constructor.
     *
     * @param ApplicationService $applicationService
     */
    public function __construct(ApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(JobPost $jobPost)
    {
        Gate::authorize('view-applications', $jobPost);
        $applications = $this->applicationService->getApplicationsForJobPost($jobPost->id);
        return ApplicationResource::collection($applications);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(JobPost $jobPost)
    {
        $application = $this->applicationService->createApplication($jobPost->id);
        return new ApplicationResource($application);
    }
} 