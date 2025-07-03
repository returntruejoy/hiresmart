<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_VIEWED = 'viewed';
    public const STATUS_SHORTLISTED = 'shortlisted';
    public const STATUS_REJECTED = 'rejected';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'job_post_id',
        'candidate_id',
        'status',
        'cover_letter',
    ];

    /**
     * Get the candidate (user) that submitted the application.
     */
    public function candidate()
    {
        return $this->belongsTo(User::class, 'candidate_id');
    }

    /**
     * Get the job post that the application is for.
     */
    public function jobPost()
    {
        return $this->belongsTo(JobPost::class);
    }
}
