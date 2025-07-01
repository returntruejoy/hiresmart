<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPost extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'job_posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employer_id',
        'title',
        'description',
        'company_name',
        'company_description',
        'location',
        'salary_min',
        'salary_max',
        'is_active',
    ];

    /**
     * Get the employer that owns the job post.
     */
    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    /**
     * Get the applications for the job post.
     */
    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    /**
     * The skills that are required for the job post.
     */
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'job_post_skill');
    }
}
