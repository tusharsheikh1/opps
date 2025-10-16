<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Campaign::class,
        Commands\SmsManagementCommand::class, // Add SMS management command
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('campaign:expire')->everyMinute();
        
        // Optional: Schedule SMS cleanup (runs daily at 2 AM)
        $schedule->command('sms:manage clean --days=30')->dailyAt('02:00');
        
        // Clean up old order restriction data daily at 2 AM
        $schedule->command('order:restriction-maintenance cleanup')->daily()->at('02:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}