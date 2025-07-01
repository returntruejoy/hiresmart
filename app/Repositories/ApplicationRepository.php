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
     *
     * @param Application $application
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
}