<?php

namespace App\Console\Commands;

use MoodleRest;
use Mail;
use App\MoodleAccount;
use App\MoodleCourse;
use App\Notes;
use App\LsaResultComparison;
use App\Note;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;

class MoodleQuizAnalysis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moodle:quiz_analysis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Всі конспекти курсів';

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
                    $dataEnrolledUsers = $endpoint->request('wsanalyticalsystem_enrolled_users', ['courseid'=>$course['xml_id']], MoodleRest::METHOD_POST);
                    
                    // все страницы (конспект) определенных курсов
                    $dataCourseContents = $endpoint->request('wsanalyticalsystem_pages_by_courses', $parametersRequest, MoodleRest::METHOD_POST);

                    // все тесты определенных курстов
                    $dataQuizzes = $endpoint->request('wsanalyticalsystem_quizzes_by_courses', $parametersRequest, MoodleRest::METHOD_POST);
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
                                $dataAttempts = $endpoint->request('wsanalyticalsystem_user_attempts', $parametersUserAttempts, MoodleRest::METHOD_POST);

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
                                    $dataAttemptReview = $endpoint->request('wsanalyticalsystem_attempt_review', $parametersRequest, MoodleRest::METHOD_POST);
        
                                    // Вопросы на которые был дан не верный ответ
                                    $questionsIncorrectAnswers = [];
                                    $questionsIncorrectAnswersId = [];
                                    foreach ($dataAttemptReview['questions'] as $arQuestion) {
                                        // собрать список вопросов, по которым был дан не верный ответ
                                        if (
											($arQuestion['status'] == 'Incorrect') || 
											($arQuestion['status'] == 'Partially correct')
											) {
                                            // сделано через костыль, решение не универсальное
                                            $tmp = explode('class="qtext">', $arQuestion['html']);
                                            $tmp2 =explode('<div class="ablock">', $tmp[1]);
                                            $textQuestion = strip_tags(str_replace("&nbsp;", " ", htmlspecialchars_decode($tmp2[0])));
        
                                            $questionsIncorrectAnswers[] = $textQuestion;
                                            $questionsIncorrectAnswersId[] = $arQuestion['parentQuestionid'];
                                        }
									}
        
                                    if (count($questionsIncorrectAnswers) > 0) {
                                        $resultData = [];
                                        foreach ($questionsIncorrectAnswers as $k=>$text) {
                                            foreach ($dataCourseContents['pages'] as $arPage) {
                                                // сохраняем в Redis
                                                // вопрос и текст, где нужно найти соотвествие
                                                $object = [
                                                    'accountId' => $account['id'], // портал Moodle
                                                    'userId' => $itemUser['id'], // пользователь
                                                    'courseId' => $course['xml_id'], // курс
                                                    'quizId' => $arQuiz['id'], // тест
                                                    'questionId' => $questionsIncorrectAnswersId[$k], // вопрос ID
                                                    'questionText' => $text, // вопрос - текст
                                                    'attemptId' => $value['id'], // вариант ответа
                                                    'pageId' => $arPage['coursemodule'], // страница конспекта
                                                    'pageText' => strip_tags(str_replace("&nbsp;", " ", htmlspecialchars_decode($arPage['content'])))
                                                ];
                                                
                                                $cacheKey = [
                                                    $account['id'],
                                                    $itemUser['id'],
                                                    $course['xml_id'],
                                                    $arQuiz['id'],
                                                    $questionsIncorrectAnswersId[$k],
                                                    $value['id'],
                                                    $arPage['coursemodule']
                                                ];

                                                // найти результата в базе вопрос-конспект
                                                $isActive = false;
                                                $tmpRes = [];

                                                foreach($arrLsaResultComparison as $itemResult){
                                                    if(
                                                        (IntVal($itemResult['account_id']) === IntVal($object['accountId'])) && 
                                                        (IntVal($itemResult['course_id']) === IntVal($object['courseId'])) && 
                                                        (IntVal($itemResult['question_id']) === IntVal($object['questionId'])) && 
                                                        (IntVal($itemResult['page_id']) === IntVal($object['pageId']))
                                                    ){
                                                        $isActive = true;
                                                        $tmpRes = $itemResult;
                                                    }
                                                }

                                                $stamp = md5(serialize($cacheKey));

                                                if (!$isActive) {
                                                    $stringData = base64_encode(json_encode($object));

                                                    Redis::hset('lsa', $stamp, $stringData);

                                                    // вызвать py скрипт для анализа текущей записи
                                                    $command = 'C:/Python38/python '.str_replace("\\", "/", base_path()).'/scripts/lsa/point.py '.$stamp;
                                                    $strLsaAnalysisResult = shell_exec($command);
                                                    $arrLsaAnalysisResult = explode("|", $strLsaAnalysisResult);

                                                    $object['params'] = $arrLsaAnalysisResult[1];
                                                    $object['status'] = $arrLsaAnalysisResult[0];
                                                }
                                                else{
                                                    $object['params'] = $tmpRes['params'];
                                                    $object['status'] = $tmpRes['status'];
                                                }

                                                // если результат сравнения не соответствует вопрос-компект,
                                                // тогда этот объект незаносим в коллекцию 
                                                // формирования конечного уведомления
                                                if (IntVal($object['status']) > 0) {
                                                    $resultData[] = $object;
                                                }
                                            }
                                        }

                                        // записываем в БД notes
                                        if(count($resultData) > 0){
                                            // save
                                            foreach($resultData as $item){
                                                $note = new Note;

                                                $note->account_id = $item['accountId'];
                                                $note->course_id = $item['courseId'];
                                                $note->user_id = $item['userId'];
                                                $note->quiz_id = $item['quizId'];
                                                $note->page_id = $item['pageId'];
                                                $note->question_content = $item['questionText'];
                                                $note->question_id = $item['questionId'];
                                                $note->attempt_id = $item['attemptId'];
                                                $note->status = 'ready';
                                                
                                                $note->save();
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
            
        $countLine = count($dataCourseContents['pages']);

        $count = 0;

        foreach ($dataCourseContents['pages'] as $key => $data) {
            $bar = $this->output->createProgressBar($countLine);
            
        }

        if (count($dataCourseContents['pages']) > 0) {
            $bar->advance();
            $bar->finish();
        }
    }

    private function p($object){
        echo '<pre>'.print_r($object, true).'</pre>';
    }
}
