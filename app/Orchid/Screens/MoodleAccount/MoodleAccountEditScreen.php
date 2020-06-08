<?php
namespace App\Orchid\Screens\MoodleAccount;

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

class MoodleAccountEditScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Moodle account';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'Порталы по которым ведется анализ пройденных тестов';

    /**
     * @var bool
     */
    public $exists = false;

    /**
     * Query data.
     *
     * @param MoodleAccount $account
     *
     * @return array
     */
    public function query(MoodleAccount $account): array
    {
        $this->exists = $account->exists;

        if($this->exists){
            $this->name = 'Редактировать';
        }

        return [
            'accounts' => $account
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
                Input::make('account.full_name')
                    ->title('ФИО'),

                Input::make('account.login')
                    ->title('Логин'),

                Input::make('account.pass')
                    ->title('Пароль'),

            ])
        ];
    }

    /**
     * @param MoodleAccount    $account
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate(MoodleAccount $account, Request $request)
    {
        $account->fill($request->get('account'))->save();

        Alert::info('Ваши данные успешно сохранены');

        return redirect()->route('platform.moodleaccount.list');
    }

    /**
     * @param MoodleAccount $account
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function remove(MoodleAccount $account)
    {
        $account->delete()
            ? Alert::info('Вы успешно удалили запись.')
            : Alert::warning('Произошла ошибка')
        ;

        return redirect()->route('platform.moodleaccount.list');
    }
}