<?php
namespace App\Orchid\Screens\Moodle;

use App\MoodleCourse;
use App\MoodleAccount;
use App\User;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;

class CourseEditScreen extends Screen
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
     * @var bool
     */
    public $exists = false;

    /**
     * Query data.
     *
     * @param MoodleCourse $course
     *
     * @return array
     */
    public function query(MoodleCourse $course): array
    {
        $this->exists = $course->exists;

        if($this->exists){
            $this->name = 'Редактировать';
        }

        return [
            'course' => $course
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
            Button::make('Создать')
                ->icon('icon-pencil')
                ->method('createOrUpdate')
                ->canSee(!$this->exists),

            Button::make('Редактировать')
                ->icon('icon-note')
                ->method('createOrUpdate')
                ->canSee($this->exists),

            Button::make('Удалить')
                ->icon('icon-trash')
                ->method('remove')
                ->canSee($this->exists),
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
            Layout::rows([
                Relation::make('course.account_id')
                    ->title('Портал')
                    ->fromModel(MoodleAccount::class, 'full_name'),

                Input::make('course.full_name')
                    ->title('Название курса'),

                Input::make('course.xml_id')
                    ->title('ID курса во внешней системе'),
            ])
        ];
    }

    /**
     * @param MoodleCourse    $course
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate(MoodleCourse $course, Request $request)
    {
        $course->fill($request->get('course'))->save();

        Alert::info('Ваши данные успешно сохранены');

        return redirect()->route('platform.moodlecourse.list');
    }

    /**
     * @param MoodleCourse $course
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function remove(MoodleCourse $course)
    {
        $course->delete()
            ? Alert::info('Вы успешно удалили запись.')
            : Alert::warning('Произошла ошибка')
        ;

        return redirect()->route('platform.moodlecourse.list');
    }
}