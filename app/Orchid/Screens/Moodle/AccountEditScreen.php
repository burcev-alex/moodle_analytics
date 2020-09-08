<?php
namespace App\Orchid\Screens\Moodle;

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

class AccountEditScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Портали';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'Портали за якими ведеться аналіз пройдених тестів';

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
            $this->name = 'Редагувати';
        }

        return [
            'account' => $account
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
            Button::make('Створити')
                ->icon('icon-pencil')
                ->method('createOrUpdate')
                ->canSee(!$this->exists),

            Button::make('Редагувати')
                ->icon('icon-note')
                ->method('createOrUpdate')
                ->canSee($this->exists),

            Button::make('Видалити')
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
                    ->title('Назва порталу')
                    ->placeholder('MDPU'),

                Input::make('account.domain')
                    ->title('Домен')
                    ->placeholder('dfn.mdpu.org.ua'),

                Input::make('account.endpoint')
                    ->title('Точка доступу (url)')
                    ->placeholder('http://dfn.mdpu.org.ua/webservice/rest/server.php'),

                Input::make('account.api_key')
                    ->title('Token'),

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

        Alert::info('Ваші дані успішно збережені');

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
            ? Alert::info('Ви успішно видалили запис.')
            : Alert::warning('Виникла помилка')
        ;

        return redirect()->route('platform.moodleaccount.list');
    }
}