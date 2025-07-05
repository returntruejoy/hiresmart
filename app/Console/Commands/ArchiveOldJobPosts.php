<?php

namespace App\Console\Commands;

use App\Models\JobPost;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ArchiveOldJobPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:archive-old-job-posts {--days=30 : Number of days after which to archive jobs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive job posts that are older than the specified number of days (default: 30)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);

        $this->info("Archiving job posts older than {$days} days (before {$cutoffDate->format('Y-m-d')})...");

        // Get count of jobs that will be archived
        $jobsToArchive = JobPost::where('is_active', true)
            ->where('created_at', '<', $cutoffDate)
            ->count();

        if ($jobsToArchive === 0) {
            $this->info('No job posts found to archive.');

            return 0;
        }

        // Archive the jobs
        $archivedJobs = JobPost::where('is_active', true)
            ->where('created_at', '<', $cutoffDate)
            ->update(['is_active' => false]);

        $this->info("Successfully archived {$archivedJobs} job post(s).");

        // Log the action
        Log::info('Job archiving completed', [
            'archived_count' => $archivedJobs,
            'cutoff_date' => $cutoffDate->format('Y-m-d'),
            'days_threshold' => $days,
        ]);

        return 0;
    }
}
