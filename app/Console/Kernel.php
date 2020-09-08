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
<<<<<<< HEAD

        // сбор сведений о пройденных тестах
        $schedule->command('moodle:course')->everyMinute()->between('06:00', '22:00')->withoutOverlapping();

        // обработка очередь для Python скрипта
        $schedule->command('analytics:lsa')->everyMinute()->between('06:00', '22:00')->after(function (Schedule $schedule) {
            // по завершению обработки, отправляем сообщение пользователю
            $schedule->command('moodle:notes');
          })->withoutOverlapping();
=======
        
        // выгрузка вопрос-конспект для LSA анализа
        $schedule->command('moodle:export_quiz')->dailyAt('22:00');

        // поиск пройденных тестов, сбор статистики
        $schedule->command('moodle:quiz_analysis')->everyMinute()->between('7:00', '20:00');

        // отчет анализа, отправка уведомления пользователю проходивший тест
        $schedule->command('moodle:notes')->everyMinute()->between('7:00', '22:00');
>>>>>>> 02144d003f20d7c0685aad1958c4d58f887732b2
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
