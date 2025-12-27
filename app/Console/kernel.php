<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Register your commands here
        \App\Console\Commands\GenerateMessBills::class,
        \App\Console\Commands\SendDailyAttendanceReport::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // Send daily attendance report at 11:00 IST
        $schedule->command('report:daily-attendance')
            ->dailyAt('16:20') // IST time
            ->withoutOverlapping()
            ->before(function () {
                Log::info('Scheduler triggered: report:daily-attendance');
            })
            ->after(function () {
                Log::info('Scheduler finished: report:daily-attendance');
            });

        // Mess bills generation
        $schedule->command('mess:generate-bills', ['--month' => now()->subMonth()->format('Y-m')])
                ->monthlyOn(1, '00:01')
                ->withoutOverlapping();
    }


    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
