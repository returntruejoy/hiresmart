<?php

namespace App\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);

            // Archive old job posts daily at 2:00 AM
            $schedule->command('app:archive-old-job-posts')
                ->daily()
                ->at('02:00')
                ->appendOutputTo(storage_path('logs/job-archiving.log'));

            // Remove unverified users weekly on Sunday at 3:00 AM
            $schedule->command('app:remove-unverified-users')
                ->weekly()
                ->sundays()
                ->at('03:00')
                ->appendOutputTo(storage_path('logs/user-cleanup.log'));
        });
    }
}
