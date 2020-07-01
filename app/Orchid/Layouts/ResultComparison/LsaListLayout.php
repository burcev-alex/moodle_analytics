<?php

namespace App\Orchid\Layouts\ResultComparison;

use App\MoodleCourse;
use App\LsaResultComparison;
use App\MoodleAccount;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;

class LsaListLayout extends Table
{
    /**
     * Data source.
     *
     * @var string
     */
    public $target = 'results';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::set('id', 'ID')
                ->render(function (LsaResultComparison $post) {
                    return Link::make($post->id)
                        ->route('platform.lsaresultcomparison.edit', $post);
                }),
            TD::set('account_id', 'Портал')
                ->render(function (LsaResultComparison $post) {
                    return $post->account['full_name'];
                }),
            TD::set('course_id', 'Курс')
                ->render(function (LsaResultComparison $post) {
                    return Link::make($post->full_name);
                }),
            TD::set('params', 'Результат')
                ->render(function (LsaResultComparison $post) {
                    return Link::make($post->params);
                }),
            TD::set('status', 'Статус')
                ->render(function (LsaResultComparison $post) {
                    return Link::make($post->status);
                })
        ];
    }
}