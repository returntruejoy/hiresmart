<?php

namespace App\Policies;

use App\Models\JobPost;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class JobPostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, JobPost $jobPost)
    {
        return $user->id === $jobPost->employer_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, JobPost $jobPost)
    {
        return $user->id === $jobPost->employer_id;
    }

    /**
     * Determine whether the user can view applications for the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewApplications(User $user, JobPost $jobPost)
    {
        return $user->id === $jobPost->employer_id;
    }
}
