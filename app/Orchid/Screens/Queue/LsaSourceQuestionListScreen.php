<?php
namespace App\Orchid\Screens\Queue;

use App\Orchid\Layouts\Queue\LsaSourceQuestionListLayout;
use Illuminate\Support\Facades\Redis;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Orchid\Screen\Repository;


class LsaSourceQuestionListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Очередь LSA анализа. Исходники';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'Команды для скрипта анализа данных';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        $result = new Arr();
        $data = Redis::hgetall('lsa_source');

        $iteration = 0;
        foreach($data as $key=>$object){
            $item = json_decode(base64_decode($object), true);

            $element = new Repository([
                'pageId' => $item['pageId'],
                'courseId' => $item['courseId'],
                'quizId' => $item['quizId'],
                'questionId' => $item['questionId'],
                'questionText' => $item['questionText'],
                'attemptId' => $item['attemptId'],
                'pageText' => substr($item['pageText'], 0, 100).'...'
            ]);

            $result->{$iteration} = $element;

            $iteration++;
        }
        
        return [
            'history' => $result,
        ];
    }

    /**
     * Button commands.
     *
     * @return Link[]
     */
    public function commandBar(): array
    {
        return [];
    }

    /**
     * Views.
     *
     * @return Layout[]
     */
    public function layout(): array
    {
        return [
            LsaSourceQuestionListLayout::class
        ];
    }
}