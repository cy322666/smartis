<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         $schedule->command('smartis:leads')->thursdays()->at('22:00:00');

         $schedule->command('smartis:info1')->thursdays()->at('22:05:00');
         $schedule->command('smartis:info2')->thursdays()->at('22:10:00');

         $schedule->command('smartis:send')->thursdays()->at('22:20:00');
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
