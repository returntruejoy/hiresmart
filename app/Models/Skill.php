<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The job posts that require this skill.
     */
    public function jobPosts()
    {
        return $this->belongsToMany(JobPost::class, 'job_post_skill');
    }

    /**
     * The users (candidates) that have this skill.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_skill');
    }
}
