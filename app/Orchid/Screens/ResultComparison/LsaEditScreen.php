<?php
namespace App\Orchid\Screens\ResultComparison;

use App\MoodleCourse;
use App\MoodleAccount;
use App\LsaResultComparison;
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

class LsaEditScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Результат LSA анализа';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'Совпадение вопрос-конспекта';

    /**
     * @var bool
     */
    public $exists = false;

    /**
     * Query data.
     *
     * @param LsaResultComparison $lsaResult
     *
     * @return array
     */
    public function query(LsaResultComparison $lsaResult): array
    {
        $this->exists = $lsaResult->exists;

        if($this->exists){
            $this->name = 'Редактировать';
        }

        return [
            'lsaResult' => $lsaResult
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
                Relation::make('lsaResult.account_id')
                    ->title('Портал')
                    ->fromModel(MoodleAccount::class, 'full_name'),
                Relation::make('lsaResult.course_id')
                    ->title('Курс')
                    ->fromModel(MoodleCourse::class, 'full_name'),

                Input::make('lsaResult.question_id')
                    ->title('Вопрос ID'),

                TextArea::make('lsaResult.question_content')
                    ->title('Вопрос(текст)'),

                Input::make('lsaResult.page_id')
                    ->title('Конспект ID'),
    
                TextArea::make('lsaResult.page_content')
                    ->title('Конспект(текст)'),
                        
                Input::make('lsaResult.params')
                    ->title('Значение LSA'),

                Input::make('lsaResult.status')
                    ->title('результат'),
            ])
        ];
    }

    /**
     * @param LsaResultComparison    $lsaResult
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate(LsaResultComparison $lsaResult, Request $request)
    {
        $lsaResult->fill($request->get('lsaResult'))->save();

        Alert::info('Ваши данные успешно сохранены');

        return redirect()->route('platform.lsaresultcomparison.list');
    }

    /**
     * @param LsaResultComparison $lsaResult
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function remove(LsaResultComparison $lsaResult)
    {
        $lsaResult->delete()
            ? Alert::info('Вы успешно удалили запись.')
            : Alert::warning('Произошла ошибка')
        ;

        return redirect()->route('platform.lsaresultcomparison.list');
    }
}