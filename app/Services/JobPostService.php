<?php

namespace App\Services;

use App\Repositories\JobPostRepository;
use Illuminate\Support\Facades\Auth;

class JobPostService
{
    /**
     * @var JobPostRepository
     */
    protected $jobPostRepository;

    /**
     * JobPostService constructor.
     *
     * @param JobPostRepository $jobPostRepository
     */
    public function __construct(JobPostRepository $jobPostRepository)
    {
        $this->jobPostRepository = $jobPostRepository;
    }

    public function createJobPost(array $data)
    {
        $data['employer_id'] = Auth::id();
        return $this->jobPostRepository->create($data);
    }

    public function updateJobPost(array $data, $id)
    {
        return $this->jobPostRepository->update($data, $id);
    }

    public function deleteJobPost($id)
    {
        return $this->jobPostRepository->delete($id);
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
}