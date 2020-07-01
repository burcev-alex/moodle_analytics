<?php

namespace App\Console\Commands;

use MoodleRest;
use Mail;
use App\MoodleAccount;
use App\Note;
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
        
        $notes = Note::all()->toArray();

        // группировка по пользователю
        $data = [];
        foreach($notes as $value){
            $data[$value['user_id']]['ids'][] = $value['id'];
            $data[$value['user_id']]['questions'][] = $value;
            $data[$value['user_id']]['account'] = $value['account_id'];
        }

        $accounts = MoodleAccount::all()->toArray();
        foreach ($data as $userId => $items) {

            // найти данные аккаунта
            $itemAccount = [];
            foreach($accounts as $value){
                if($value['id'] == $items['account']){
                    $itemAccount = $value;
                }
            }

            try {
                $endpoint = new MoodleRest($itemAccount['endpoint'], $itemAccount['api_key']);

                $parametersRequest = [];
                
                // группировка по вопросам
                $questions = [];
                foreach ($items['questions'] as $key=>$item) {
                    $questions[$item['question_id']]['id'] = $item['question_id'];
                    $questions[$item['question_id']]['text'] = $item['question_content'];
                    $questions[$item['question_id']]['pages'][] = $item['page_id'];
                    
                }

                foreach ($questions as $questionId => $item) {
                    $message = "Вы дали не верный ответ на вопрос: <br/>".$item['text'] . "<br/>";
                    $message .= "Ответ вы можете найти здесь: </br>";
                    foreach ($item['pages'] as $pageId) {
                        $message .= "<a href='/mod/page/view.php?id=".$pageId."'>конспект</a><br/>";
                    }
                }

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
            }
            catch (\ErrorException $e){
                dd($e->getMessage());
            }
        }
            
        
    }
}
