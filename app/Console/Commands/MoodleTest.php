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

class MoodleTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moodle:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Данные moodle-системы. Проверочные';

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
        $courses = MoodleCourse::all()->toArray();
        $dataCourseContents = [];

        $accounts = MoodleAccount::all()->toArray();
        foreach ($accounts as $key => $account) {
            if($account['id'] != 2) continue;


            try {
                $endpoint = new MoodleRest($account['endpoint'], $account['api_key']);

                foreach ($courses as $course) {
                    if ($course['account_id'] == $account['id']) {
                        
                        $parametersRequest = [
                            "welcomemessage" => $course['xml_id'],
                        ];

                        // Возвращает список экземпляров опроса в предоставленном наборе курсов.
                        // Если курсы не предоставлены, будут возвращены все экземпляры опроса,
                        // к которым у пользователя есть доступ.
                        $dataQuestions = $endpoint->request('local_wsanalyticalsystem_hello_world', $parametersRequest, MoodleRest::METHOD_POST);

                        var_dump($dataQuestions);

                        $parametersRequest = [
                            "courseId" => $course['xml_id'],
                        ];

                        // Возвращает список экземпляров опроса в предоставленном наборе курсов.
                        // Если курсы не предоставлены, будут возвращены все экземпляры опроса,
                        // к которым у пользователя есть доступ.
                        $dataQuestions = $endpoint->request('wsanalyticalsystem_question_list_by_courses', $parametersRequest, MoodleRest::METHOD_POST);

                        dd($dataQuestions);
                    }
                }

                unset($endpoint);
            }
            catch (\ErrorException $e){
                dd($e->getMessage());
            }
        }
            
        
    }
}
