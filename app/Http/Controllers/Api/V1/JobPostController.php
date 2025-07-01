<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\JobPostService;
use App\Http\Requests\Api\V1\JobPostRequest;
use App\Http\Resources\Api\V1\JobPostResource;
use App\Models\JobPost;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class JobPostController extends Controller
{
    /**
     * @var JobPostService
     */
    protected $jobPostService;

    /**
     * JobPostController constructor.
     *
     * @param JobPostService $jobPostService
     */
    public function __construct(JobPostService $jobPostService)
    {
        $this->jobPostService = $jobPostService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobPosts = $this->jobPostService->getJobPostsForCurrentUser();
        return JobPostResource::collection($jobPosts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(JobPostRequest $request)
    {
        $jobPost = $this->jobPostService->createJobPost($request->validated());
        return new JobPostResource($jobPost);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(JobPostRequest $request, JobPost $jobPost)
    {
        Gate::authorize('update', $jobPost);
        $updatedJobPost = $this->jobPostService->updateJobPost($request->validated(), $jobPost->id);
        return new JobPostResource($updatedJobPost);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobPost $jobPost)
    {
        Gate::authorize('delete', $jobPost);
        $this->jobPostService->deleteJobPost($jobPost->id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
} 