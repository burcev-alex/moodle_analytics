<?php

namespace App\Console;

use App\SchedulePage;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $userId;

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        set_time_limit(0);
        
        // валидация прокси-серверов
        $schedule->command('validator:proxy')->dailyAt('03:00');

        // генерация задач на день
        $schedule->command('generation:schedule')->dailyAt('05:00');

        // выполнение задач
        $schedule->command('visiting:page')->everyMinute()->between('7:00', '20:00');

        // отчет посещения за предыдущий день
        $schedule->command('visiting:report')->dailyAt('06:00');
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
