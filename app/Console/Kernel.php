<?php

namespace App\Console;

use App\Console\Commands\MigrateLaptopsToProducts;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array<int, class-string>
     */
    protected $commands = [
        MigrateLaptopsToProducts::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Define scheduled tasks here if needed
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        // You may load commands by directory if you prefer:
        // $this->load(__DIR__.'/Commands');
    }
}
