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

        // сбор сведений о пройденных тестах
        $schedule->command('moodle:course')->everyMinute()->between('06:00', '22:00')->withoutOverlapping();

        // обработка очередь для Python скрипта
        $schedule->command('analytics:lsa')->everyMinute()->between('06:00', '22:00')->after(function (Schedule $schedule) {
            // по завершению обработки, отправляем сообщение пользователю
            $schedule->command('moodle:notes');
          })->withoutOverlapping();
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
