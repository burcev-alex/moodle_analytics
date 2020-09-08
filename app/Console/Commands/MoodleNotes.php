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

class MoodleNotes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moodle:notes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отправка сообщений пользователю moodle';

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
        $accounts = MoodleAccount::all()->toArray();

        $queue = Redis::hgetall('notes');
        $countLine = count($queue);
        
        foreach ($queue as $key => $item) {
            dd($item);
            $bar = $this->output->createProgressBar($countLine);
        }

        if (count($queue) > 0) {
            $bar->advance();

            $bar->finish();
        }
    }
}
