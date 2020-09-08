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
            TD::set('id.', 'ID')
                ->render(function (LsaResultComparison $post) {
                    return Link::make($post->id)
                        ->route('platform.lsaresultcomparison.edit', $post);
                }),
            TD::set('account_id', 'Портал')
                ->render(function (LsaResultComparison $post) {
                    return $post->account['full_name'];
                }),
            TD::set('course_id', 'Курс')->render(function (LsaResultComparison $post) {
                return "<a target='_blank' href='http://".$post->account['domain']."/course/view.php?id=".$post->course_id."'>".$post->course["full_name"]."</a>";
            }),
            TD::set('question_id', 'Вопрос')->render(function (LsaResultComparison $post) {
                return "<a target='_blank' href='http://".$post->account['domain']."/question/preview.php?id=".$post->question_id."&courseid=".$post->course_id."'>".$post->question_id."</a>";
            }),
            TD::set('page_id', 'Конспект')->render(function (LsaResultComparison $post) {
                return "<a target='_blank' href='http://".$post->account['domain']."/mod/page/view.php?id=".$post->page_id."'>".$post->page_id."</a>";
            }),
            TD::set('params', 'Результат'),
            TD::set('status', 'Статус')->render(function (LsaResultComparison $post) {
                if(IntVal($post->status) == 1){
                    return 'success';
                }
                else{
                    return 'error';
                }
            }),
        ];
    }
}