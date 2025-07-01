<?php

namespace App\Repositories;

use App\Models\JobPost;

class JobPostRepository
{
    /**
     * @var JobPost
     */
    protected $model;

    /**
     * JobPostRepository constructor.
     *
     * @param JobPost $jobPost
     */
    public function __construct(JobPost $jobPost)
    {
        $this->model = $jobPost;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        $record = $this->model->findOrFail($id);
        $record->update($data);
        return $record;
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function getForUser($userId)
    {
        return $this->model->where('user_id', $userId)->latest()->get();
    }
} 