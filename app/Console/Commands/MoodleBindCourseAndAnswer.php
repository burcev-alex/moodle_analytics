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

class MoodleBindCourseAndAnswer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moodle:course';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Все конспекты курсов';

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
            $endpoint = new MoodleRest($account['endpoint'], $account['api_key']);

            foreach ($courses as $course) {
                if($course['account_id'] == $account['id']){
                    $parametersRequest = [
                        "courseids" => [
                            $course['xml_id']
                        ]
                    ];

                    // Список пользователей которые подписаны на курс
                    $dataEnrolledUsers = $endpoint->request('core_enrol_get_enrolled_users', ['courseid'=>$course['xml_id']], MoodleRest::METHOD_POST);
                    
                    // все страницы (конспект) определенных курстов
                    $dataCourseContents = $endpoint->request('mod_page_get_pages_by_courses', $parametersRequest, MoodleRest::METHOD_POST);

                    // все тесты определенных курстов
                    $dataQuizzes = $endpoint->request('mod_quiz_get_quizzes_by_courses', $parametersRequest, MoodleRest::METHOD_POST);
                    if (count($dataQuizzes["quizzes"]) > 0) {
                        foreach ($dataQuizzes["quizzes"] as $arQuiz) {

                            // проходим по всем пользователям курса
                            foreach($dataEnrolledUsers as $itemUser){

                                // выборка всех попыток прохождения тестов
                                $parametersUserAttempts = [
                                    "quizid" => $arQuiz['id'],
                                    "userid" => $itemUser['id']
                                ];
        
                                // Вернуть список попыток для данного теста и пользователя.
                                $dataAttempts = $endpoint->request('mod_quiz_get_user_attempts', $parametersUserAttempts, MoodleRest::METHOD_POST);

                                foreach ($dataAttempts['attempts'] as $key => $value) {
                                    // только завершенное прохождение тестов
                                    if ($value['state'] != 'finished') {
                                        continue;
                                    }
        
                                    // посмотреть детальную информацию по каждой попытке
                                    $parametersRequest = [
                                        "attemptid" => $value['id']
                                    ];
                            
                                    // Вернуть список попыток для данного теста и пользователя.
                                    $dataAttemptReview = $endpoint->request('mod_quiz_get_attempt_review', $parametersRequest, MoodleRest::METHOD_POST);
        
                                    // Вопросы на которые был дан не верный ответ
                                    $questionsIncorrectAnswers = [];
                                    $questionsIncorrectAnswersId = [];
                                    foreach ($dataAttemptReview['questions'] as $arQuestion) {
                                        // собрать список вопросов, по которым был дан не верный ответ
                                        if ($arQuestion['status'] == 'Incorrect') {
                                            // сделано через костыль, решение не универсальное
                                            $tmp = explode('class="qtext">', $arQuestion['html']);
                                            $tmp2 =explode('<div class="ablock">', $tmp[1]);
                                            $textQuestion = strip_tags(str_replace("&nbsp;", " ", htmlspecialchars_decode($tmp2[0])));
        
                                            $questionsIncorrectAnswers[] = $textQuestion;
                                            $questionsIncorrectAnswersId[] = $arQuestion['number'];
                                        }
                                    }
        
                                    if (count($questionsIncorrectAnswers) > 0) {
                                        foreach ($questionsIncorrectAnswers as $k=>$text) {
                                            foreach ($dataCourseContents['pages'] as $arPage) {
                                                // сохраняем в Redis
                                                // вопрос и текст, где нужно найти соотвествие
                                                $object = [
                                                    'pageId' => $arPage['coursemodule'],
                                                    'courseId' => $course['xml_id'],
                                                    'quizId' => $arQuiz['id'],
                                                    'questionId' => $questionsIncorrectAnswersId[$k],
                                                    'questionText' => $text,
                                                    'attemptId' => $value['id'],
                                                    'pageText' => strip_tags(str_replace("&nbsp;", " ", htmlspecialchars_decode($arPage['content'])))
                                                ];

                                                Redis::hset('lsa', microtime(true), base64_encode(json_encode($object)));
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            

            unset($endpoint);
        }
            
        $countLine = count($courses);

        $count = 0;
        #dd(count($dataCourseContents['pages']));

        foreach ($dataCourseContents['pages'] as $key => $data) {
            $bar = $this->output->createProgressBar($countLine);
            
        }

        if (count($courses) > 0) {
            $bar->advance();

            $bar->finish();
        }
    }
}
