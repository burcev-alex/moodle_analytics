<?php

namespace App\Orchid\Layouts\Queue;

use Illuminate\Support\Collection;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Illuminate\Support\Arr;

class LsaListLayout extends Table
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
            TD::set('pageId', 'Страница'),
            TD::set('courseId', 'Курс'),
            TD::set('quizId', 'Тест'),
            TD::set('questionText', 'Вопрос'),
            TD::set('attemptId', 'Ответ #'),
            TD::set('pageText', 'Конспект'),
        ];
    }
}