<?php

namespace App\Console\Commands;

use MoodleRest;
use Mail;
use App\MoodleAccount;
use App\MoodleCourse;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;

class AnalyticsLsa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:lsa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обработка очередь LSA анализа, через Python script';

    protected $rows;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $queue = Redis::hgetall('lsa');
        $countLine = count($queue);
        
        foreach ($queue as $key => $item) {
            $bar = $this->output->createProgressBar($countLine);

            $output = '';
            // интерпритатор.скрипт - имя очереди - ключ очереди
            $command = 'C:/Python38/python '.str_replace("\\", "/", public_path()).'/services/lsa/run.py moodle_ml_database_lsa '.$key;
            exec($command, $output);

            // результат анализа . SUCCESS or ERROR
            $status = $output[0];

            #var_dump($status);
            
        }

        if (count($queue) > 0) {
            $bar->advance();

            $bar->finish();
        }
    }
}
