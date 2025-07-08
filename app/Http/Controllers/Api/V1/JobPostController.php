<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\JobPostRequest;
use App\Http\Resources\Api\V1\JobPostResource;
use App\Models\JobPost;
use App\Services\JobPostService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class JobPostController extends Controller
{
    use ApiResponseTrait;

    protected JobPostService $jobPostService;

    public function __construct(JobPostService $jobPostService)
    {
        $this->jobPostService = $jobPostService;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user && $user->isEmployer()) {
            $jobs = $this->jobPostService->getJobsByEmployer($user->id);
        } else {
            $jobs = $this->jobPostService->getAllActiveJobs();
        }

        return $this->successResponse(
            JobPostResource::collection($jobs),
            'Successfully retrieved job posts.'
        );
    }

    public function indexForEmployer()
    {
        $jobPosts = $this->jobPostService->getJobPostsForCurrentUser();

        return $this->successResponse(
            JobPostResource::collection($jobPosts),
            'Successfully retrieved employer job posts.'
        );
    }

    public function show(JobPost $jobPost)
    {
        return $this->successResponse(
            new JobPostResource($jobPost->load('skills')),
            'Successfully retrieved job post.'
        );
    }

    public function store(JobPostRequest $request)
    {
        $jobPost = $this->jobPostService->createJobPost($request->validated());

        return $this->createdResponse(
            new JobPostResource($jobPost),
            'Job post created successfully.'
        );
    }

    public function update(JobPostRequest $request, JobPost $jobPost)
    {
        Gate::authorize('update', $jobPost);
        $updatedJobPost = $this->jobPostService->updateJobPost($request->validated(), $jobPost->id);

        return $this->successResponse(
            new JobPostResource($updatedJobPost),
            'Job post updated successfully.'
        );
    }

    public function destroy(JobPost $jobPost)
    {
        Gate::authorize('delete', $jobPost);
        $this->jobPostService->deleteJobPost($jobPost->id);

        return $this->noContentResponse();
    }

    public function cacheStats()
    {
        $stats = $this->jobPostService->getCacheStats();

        return $this->successResponse($stats, 'Cache statistics retrieved successfully.');
    }

    public function clearCache()
    {
        $this->jobPostService->clearCache();

        return $this->successResponse(null, 'Job listings cache cleared successfully.');
    }
}
