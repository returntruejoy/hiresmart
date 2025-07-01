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

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function getForEmployer($employerId)
    {
        return $this->model->where('employer_id', $employerId)->latest()->get();
    }

    public function search(array $filters = [])
    {
        $query = $this->model->query()->where('is_active', true);

        if (!empty($filters['keyword'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['keyword'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['keyword'] . '%');
            });
        }

        if (!empty($filters['location'])) {
            $query->where('location', 'like', '%' . $filters['location'] . '%');
        }

        return $query->latest()->get();
    }

    public function count(): int
    {
        return $this->model->count();
    }
} 