<?php

namespace App\Console\Commands;

use App\SchedulePage;
use App\User;
use App\Schedule;
use App\MoodleUser;
use Illuminate\Console\Command;

class GenerationSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generation:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Генерация расписания запуска команд посещения страниц Moodle';

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
        SchedulePage::truncate();

        /*
            1 пара - 08:00@09:20
            2 пара - 09:35@10:55
            3 пара - 11:10@12:30
            4 пара - 12:45@14:05
            5 пара - 14:20@15:40
            6 пара - 15:55@17:15
            7 пара - 17:30@18:50
            8 пара - 19:05@20:25
        */

        $lessonList = [
            1 => '08:00@09:20',
            2 => '09:35@10:55',
            3 => '11:10@12:30',
            4 => '12:45@14:05',
            5 => '14:20@15:40',
            6 => '15:55@17:15',
            7 => '17:30@18:50',
            8 => '19:05@20:25'
        ];

        $scheduleList = [];

        $items = Schedule::all()->toArray();
        foreach($items as $key=>$item){
            $arLink = explode("\r\n", $item['link']);
            
            // время пары
            $timeStart = $lessonList[IntVal($item['lesson_number'])];

            $scheduleList[$item['user_id']][$item['type_week']][$item['day']][$timeStart] = $arLink;
        }

        if (count($scheduleList) > 0) {
            $list = [];
            foreach ($scheduleList as $user_id=>$item) {
                if (array_key_exists($this->getCodeWeek(), $item)) {
                    if (array_key_exists(date("N", time()), $item[$this->getCodeWeek()])) {
                        foreach ($item[$this->getCodeWeek()][date("N", time())] as $times => $link) {
                            $time = explode('@', $times);
                            $time_from = strtotime(date('d-m-Y ', time()).$time[0]);
                            $time_to = strtotime(date('d-m-Y ', time()).$time[1]);

                            $i = $time_from;

                            $list[] = [
                                'type' => 'auth',
                                'user_id' => $user_id,
                                'time_start' => $i,
                                'link' => "/login/index.php"
                            ];

                            do {
                                $i = $i + rand(65, 200);

                                shuffle($link);

                                $randomLink = $link[0];
                        
                                $list[] = [
                                    'type' => 'page',
                                    'user_id' => $user_id,
                                    'time_start' => $i,
                                    'link' => $randomLink
                                ];
                            } while ($i <= $time_to);
                        }
                    }
                } 
            }
        }

            
        $countLine = count($list);

        foreach ($list as $key => $data) {
            $bar = $this->output->createProgressBar($countLine);

            $this->save($data);
        }

        if (count($list) > 0) {
            $bar->advance();

            $bar->finish();
        }
    }

    /**
     * Определить какая неделя четная а какая нечетная
     * зелена  - четная
     * красная - нечетная
     *
     * @return void
     */
    protected function getCodeWeek()
    {
        return date("W", time())%2==0 ? 'green' : 'red';
    }

    protected function save($data)
    {
        $fields = new SchedulePage();
        $fields->fill($data);
        $fields->save();

        $id = $fields->id;
    }
}
