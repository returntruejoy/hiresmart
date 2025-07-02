<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RemoveUnverifiedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-unverified-users {--days=7 : Number of days after which to remove unverified users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove users who have not verified their email within the specified number of days (default: 7)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);

        $this->info("Removing unverified users older than {$days} days (before {$cutoffDate->format('Y-m-d')})...");

        // Get count of unverified users that will be removed
        $usersToRemove = User::whereNull('email_verified_at')
            ->where('created_at', '<', $cutoffDate)
            ->count();

        if ($usersToRemove === 0) {
            $this->info('No unverified users found to remove.');
            return 0;
        }

        // Show a warning and ask for confirmation if more than 10 users
        if ($usersToRemove > 10) {
            if (!$this->confirm("This will remove {$usersToRemove} unverified users. Are you sure?")) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        // Get the users before deletion for logging
        $usersToDelete = User::whereNull('email_verified_at')
            ->where('created_at', '<', $cutoffDate)
            ->get(['id', 'email', 'created_at']);

        // Remove the users (using soft delete if available)
        $removedCount = User::whereNull('email_verified_at')
            ->where('created_at', '<', $cutoffDate)
            ->delete();

        $this->info("Successfully removed {$removedCount} unverified user(s).");

        // Log the action with details
        Log::info("Unverified users cleanup completed", [
            'removed_count' => $removedCount,
            'cutoff_date' => $cutoffDate->format('Y-m-d'),
            'days_threshold' => $days,
            'removed_users' => $usersToDelete->map(function ($user) {
                return [
                    'id' => $user->id,
                    'email' => $user->email,
                    'created_at' => $user->created_at->format('Y-m-d H:i:s')
                ];
            })->toArray()
        ]);

        return 0;
    }
}
