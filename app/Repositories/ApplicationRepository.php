<?php

namespace App\Repositories;

use App\Models\Application;

class ApplicationRepository
{
    /**
     * @var Application
     */
    protected $model;

    /**
     * ApplicationRepository constructor.
     */
    public function __construct(Application $application)
    {
        $this->model = $application;
    }

    public function getForJobPost($jobPostId)
    {
        return $this->model
            ->where('job_post_id', $jobPostId)
            ->with('candidate')
            ->latest()
            ->get();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function hasAlreadyApplied($jobPostId, $candidateId): bool
    {
        return $this->model
            ->where('job_post_id', $jobPostId)
            ->where('candidate_id', $candidateId)
            ->exists();
    }

    public function count(): int
    {
        return $this->model->count();
    }

    public function countForEmployer(int $employerId): int
    {
        return $this->model->whereHas('jobPost', function ($query) use ($employerId) {
            $query->where('employer_id', $employerId);
        })->count();
    }
}
