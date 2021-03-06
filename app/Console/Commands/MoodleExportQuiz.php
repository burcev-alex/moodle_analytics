<?php

namespace App\Console\Commands;

use MoodleRest;
use Mail;
use App\MoodleAccount;
use App\LsaResultComparison;
use App\MoodleCourse;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;

class MoodleExportQuiz extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moodle:export_quiz';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Дані moodle-системи. Тести і конспекти.';

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
        
        $arrLsaResultComparison = LsaResultComparison::all()->toArray();

        $iteration = 0;

        $accounts = MoodleAccount::all()->toArray();
        foreach ($accounts as $key => $account) {

            try {
                $endpoint = new MoodleRest($account['endpoint'], $account['api_key']);

                foreach ($courses as $course) {
                    if ($course['account_id'] == $account['id']) {

                        // база вопросов курса
                        $dataQuestions = $endpoint->request(
                            'wsanalyticalsystem_question_list_by_courses', 
                            [
                                "courseId" => $course['xml_id']
                            ], 
                            MoodleRest::METHOD_POST
                        );

                        // все страницы (конспект) определенных курстов
                        $dataCourseContents = $endpoint->request(
                            'wsanalyticalsystem_pages_by_courses', 
                            [
                                'courseids' => [
                                    $course['xml_id']
                                ]
                            ], 
                            MoodleRest::METHOD_POST
                        );

                        foreach ($dataQuestions as $item) {
                            foreach ($dataCourseContents['pages'] as $arPage) {
                                // сохраняем в Redis
                                // вопрос и текст, где нужно найти соотвествие
                                $object = [
                                    'accountId' => $account['id'],
                                    'courseId' => $course['xml_id'],
									'questionId' => $item['id'],
                                    'questionText' => $item['name'],
                                    'pageId' => $arPage['coursemodule'],
                                    'pageText' => strip_tags(str_replace("&nbsp;", " ", htmlspecialchars_decode($arPage['content'])))
								];
								
								$cacheKey = [
									$object['accountId'],
									$object['courseId'],
									$object['questionId'],
									$object['pageId']
								];
								$stamp = md5(serialize($cacheKey));

								if(Redis::hget('lsa_source', $stamp)){
									continue;
								}

                                // найти соответствие, возможно уже был выполнен анализ
                                $isActive = false;
                                foreach($arrLsaResultComparison as $itemResult){
                                    if(
                                        $itemResult['account_id'] == $account['id'] && 
                                        $itemResult['course_id'] == $course['id'] && 
                                        $itemResult['question_id'] == $item['id'] && 
                                        $itemResult['page_id'] == $arPage['coursemodule']
                                    ){
                                        $isActive = true;
                                    }
                                }

                                if (!$isActive) {
                                    Redis::hset('lsa_source', $stamp, base64_encode(json_encode($object)));
                                }
                                $iteration++;
                            }
                        }

                        $this->info('Кол-во вопросов: '.count($dataQuestions));
                        $this->info('Кол-во конспектов: '.count($dataCourseContents['pages']));
                        $this->info('Кол-во пересечений: '.$iteration);
                        $this->info('==========');
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
