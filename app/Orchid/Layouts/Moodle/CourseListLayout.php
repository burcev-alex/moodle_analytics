<?php

namespace App\Orchid\Layouts\Moodle;

use App\MoodleCourse;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;

class CourseListLayout extends Table
{
    /**
     * Data source.
     *
     * @var string
     */
    public $target = 'courses';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::set('account_id', 'Портал')
                ->render(function (MoodleCourse $post) {
                    return $post->account['full_name'];
                }),
            TD::set('full_name', 'Название курса')
                ->render(function (MoodleCourse $post) {
                    return Link::make($post->full_name)
                        ->route('platform.moodlecourse.edit', $post);
                })
        ];
    }
}