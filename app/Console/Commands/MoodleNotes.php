<?php

namespace App\Console\Commands;

use MoodleRest;
use Mail;
use App\MoodleAccount;
use App\Note;
use PDF;
use Storage;
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
    protected $description = 'Отправка уведомлений пользователям о прохождении тестов и рекомендательом письме';

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
        
        $notes = Note::all();

        // группировка по пользователю
        $data = [];
        foreach($notes as $value){
            $data[$value->user_id][$value->quiz_id]['ids'][] = $value->id;
            $data[$value->user_id][$value->quiz_id]['questions'][] = [
                'question_id' => $value->question_id,
                'question_content' => $value->question_content,
                'page_id' => $value->page_id,
            ];
            $data[$value->user_id][$value->quiz_id]['account'] = $value->account_id;
            $data[$value->user_id][$value->quiz_id]['course'] = $value->course['full_name'];
        }

        $accounts = MoodleAccount::all()->toArray();
        foreach ($data as $userId => $quiz) {
            foreach ($quiz as $dataQuizId => $items) {

                // найти данные аккаунта
                $itemAccount = [];
                foreach ($accounts as $value) {
                    if ($value['id'] == $items['account']) {
                        $itemAccount = $value;
                    }
                }

                try {
                    $endpoint = new MoodleRest($itemAccount['endpoint'], $itemAccount['api_key']);

                    $parametersRequest = [];
                
                    // группировка по вопросам
                    $sourceQuestions = [];
                    foreach ($items['questions'] as $key=>$item) {
                        $sourceQuestions[$item['question_id']]['id'] = $item['question_id'];
                        $sourceQuestions[$item['question_id']]['text'] = $item['question_content'];
                        $sourceQuestions[$item['question_id']]['pages'][] = $item['page_id'];
                    }

                    $questions = [];
                    foreach ($sourceQuestions as $questionId => $item) {
                        $dataQuestion = [
                        'name' => $item['text'],
                        'id' => $item['id'],
                        'pages' => []
                    ];

                        $iteration = 0;
                        foreach ($item['pages'] as $pageId) {
                            $iteration++;
                            $dataQuestion['pages'][] = [
                            'title' => 'Конспект №'.$iteration,
                            'id' => $pageId,
                            'link' => '/mod/page/view.php?id='.$pageId
                        ];
                        }

                        $questions[] = $dataQuestion;
                    }

                    $courseName = "Меиа";
                    $quizId = $dataQuizId;
                    $dateCreate = date('d.m.Y');

                    $pdf = PDF::loadView('pdf.lsarecommend', compact('questions', 'courseName', 'quizId', 'dateCreate'));
        
                    $randString = md5(serialize($questions));
                    Storage::put('public/pdf/'.$randString.'.pdf', $pdf->output());

                    $fileName = $randString.".pdf";
                    $message = "Вы дали не верный ответ на вопросы при проходжении теста: <br/>";
                    $message = "<a href='".config('app.url')."/storage/pdf/".$fileName."'>рекоммендации по изучению курса</a>";

                    $parametersRequest['messages'][] = [
                    "touserid" => $userId,
                    "text" => $message,
                    "textformat" => 1,
                    "clientmsgid" => 1
                ];

                    // отправка уведомления
                    $endpoint->request(
                    'wsanalyticalsystem_send_messages',
                    $parametersRequest,
                    MoodleRest::METHOD_POST
                );

                    Note::destroy($items['ids']);
                
                    unset($endpoint);
                } catch (\ErrorException $e) {
                    dd($e->getMessage());
                }
            }
        }
            
        
    }
}
