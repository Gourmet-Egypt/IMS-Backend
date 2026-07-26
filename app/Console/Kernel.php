<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Commands\PrinterStatusCommand::class,
        \App\Commands\DiscoverPrintersCommand::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            app(\App\Services\PurchaseOrderPrintService::class)->cleanupOldFiles();
        })->daily();

        // Restart queue worker daily to prevent memory leaks
        $schedule->command('queue:restart')->daily();

        // Keep the Telescope SQLite log bounded: drop entries older than 48h.
        $schedule->command('telescope:prune --hours=48')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

}
