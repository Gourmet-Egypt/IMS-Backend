<?php

namespace App\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PrinterStatusCommand extends Command
{
    protected $signature = 'printer:status';
    protected $description = 'Show printer queue status and recent jobs';

    public function handle(): int
    {
        $this->info('Printer Queue Status');
        $this->newLine();

        // Pending jobs
        $pending = DB::table('jobs')
            ->where('queue', 'printing')
            ->count();

        $this->info("Pending Jobs: {$pending}");
        $this->newLine();


        $failed = DB::table('failed_jobs')
            ->where('queue', 'printing')
            ->orderBy('failed_at', 'desc')
            ->limit(10)
            ->get();

        if ($failed->isNotEmpty()) {
            $this->warn("Recent Failed Jobs: {$failed->count()}");
            $this->newLine();

            $this->table(
                ['ID', 'Failed At', 'Exception'],
                $failed->map(fn($job) => [
                    $job->id,
                    $job->failed_at,
                    substr($job->exception, 0, 100).'...',
                ])
            );
        } else {
            $this->info('No failed jobs');
        }

        return 0;
    }
}
