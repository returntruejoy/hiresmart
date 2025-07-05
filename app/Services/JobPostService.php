<?php

namespace App\Services;

use App\Repositories\JobPostRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class JobPostService
{
    /**
     * @var JobPostRepository
     */
    protected $jobPostRepository;

    /**
     * Cache key for recent job listings
     */
    const RECENT_JOBS_CACHE_KEY = 'recent_job_listings';

    const CACHE_TTL = 300;

    /**
     * JobPostService constructor.
     */
    public function __construct(JobPostRepository $jobPostRepository)
    {
        $this->jobPostRepository = $jobPostRepository;
    }

    public function createJobPost(array $data)
    {
        $data['employer_id'] = Auth::id();
        $jobPost = $this->jobPostRepository->create($data);

        $this->clearRecentJobsCache();

        return $jobPost;
    }

    public function updateJobPost(array $data, $id)
    {
        $jobPost = $this->jobPostRepository->update($data, $id);

        $this->clearRecentJobsCache();

        return $jobPost;
    }

    public function deleteJobPost($id)
    {
        $result = $this->jobPostRepository->delete($id);

        $this->clearRecentJobsCache();

        return $result;
    }

    public function getJobPostsForCurrentUser()
    {
        $userId = Auth::id();

        return $this->jobPostRepository->getForEmployer($userId);
    }

    public function searchJobPosts(array $filters)
    {
        return $this->jobPostRepository->search($filters);
    }

    public function getJobPost($id)
    {
        return $this->jobPostRepository->find($id);
    }

    public function getAllActiveJobs()
    {
        return Cache::remember(self::RECENT_JOBS_CACHE_KEY, self::CACHE_TTL, function () {
            return $this->jobPostRepository->getAllActive();
        });
    }

    private function clearRecentJobsCache(): void
    {
        Cache::forget(self::RECENT_JOBS_CACHE_KEY);
    }

    public function getCacheStats(): array
    {
        return [
            'cache_key' => self::RECENT_JOBS_CACHE_KEY,
            'ttl_seconds' => self::CACHE_TTL,
            'ttl_minutes' => self::CACHE_TTL / 60,
            'has_cache' => Cache::has(self::RECENT_JOBS_CACHE_KEY),
        ];
    }
}
