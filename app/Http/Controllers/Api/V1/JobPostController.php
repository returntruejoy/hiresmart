<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\JobPostService;
use App\Http\Requests\Api\V1\JobPostRequest;
use App\Http\Resources\Api\V1\JobPostResource;
use App\Models\JobPost;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;

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
     * Display a listing of the resource for public.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        if ($user && $user->isEmployer()) {
            $jobs = $this->jobPostService->getJobsByEmployer($user->id);
        } else {
            $jobs = $this->jobPostService->getAllActiveJobs();
        }
        
        return JobPostResource::collection($jobs);
    }

    /**
     * Display a listing of the resource for an employer.
     */
    public function indexForEmployer()
    {
        $jobPosts = $this->jobPostService->getJobPostsForCurrentUser();
        return JobPostResource::collection($jobPosts);
    }

    /**
     * Display the specified resource.
     */
    public function show(JobPost $jobPost)
    {
        return new JobPostResource($jobPost->load('skills'));
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

    /**
     * Get cache statistics for debugging
     */
    public function cacheStats()
    {
        $stats = $this->jobPostService->getCacheStats();
        
        return response()->json([
            'cache_stats' => $stats,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Clear the job listings cache manually
     */
    public function clearCache()
    {
        Cache::forget('recent_job_listings');
        
        return response()->json([
            'message' => 'Job listings cache cleared successfully',
            'timestamp' => now()->toISOString(),
        ]);
    }
} 