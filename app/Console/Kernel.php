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
        
        // выгрузка вопрос-конспект для LSA анализа
        $schedule->command('moodle:export_quiz')->dailyAt('22:00');

        // поиск пройденных тестов, сбор статистики
        $schedule->command('moodle:quiz_analysis')->everyMinute()->between('7:00', '20:00');

        // отчет анализа, отправка уведомления пользователю проходивший тест
        $schedule->command('moodle:notes')->everyMinute()->between('7:00', '22:00');
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
