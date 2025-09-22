<?php

namespace App\Console;

use App\Console\Commands\MakeContract;
use App\Console\Commands\UpdateStatusArsip;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        MakeContract::class,
        UpdateStatusArsip::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Update status arsip setiap hari pada jam 02:00
        $schedule->command('arsip:update-status')
            ->dailyAt('02:00')
            ->withoutOverlapping()
            ->runInBackground();

        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
