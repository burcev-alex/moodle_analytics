<?php

namespace App\Orchid\Layouts\Queue;

use Illuminate\Support\Collection;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Illuminate\Support\Arr;

class LsaSourceQuestionListLayout extends Table
{
    /**
     * Data source.
     *
     * @var string
     */
    public $target = 'history';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::set('pageId', 'Сторінка'),
            TD::set('courseId', 'Курс'),
            TD::set('quizId', 'Тест'),
            TD::set('questionText', 'Питання'),
            TD::set('attemptId', 'Відповідь #'),
            TD::set('pageText', 'Конспект'),
        ];
    }
}