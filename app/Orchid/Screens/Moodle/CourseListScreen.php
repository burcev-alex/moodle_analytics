<?php
namespace App\Orchid\Screens\Moodle;

use App\Orchid\Layouts\Moodle\CourseListLayout;
use App\MoodleCourse;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class CourseListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Курсы';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'Курсы которые участвуют в анализе';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'courses' => MoodleCourse::paginate()
        ];
    }

    /**
     * Button commands.
     *
     * @return Link[]
     */
    public function commandBar(): array
    {
        return [
            Link::make('Добавить')
                ->icon('icon-pencil')
                ->route('platform.moodlecourse.edit')
        ];
    }

    /**
     * Views.
     *
     * @return Layout[]
     */
    public function layout(): array
    {
        return [
            CourseListLayout::class
        ];
    }
}