<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunJobMatchingNow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-job-matching-now';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \App\Jobs\RunJobMatching::dispatch();
        $this->info('Job matching dispatched!');
    }
}
