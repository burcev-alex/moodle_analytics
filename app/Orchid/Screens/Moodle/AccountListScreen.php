<?php
namespace App\Orchid\Screens\Moodle;

use App\Orchid\Layouts\Moodle\AccountListLayout;
use App\MoodleAccount;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class AccountListScreen extends Screen
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
    public $description = 'Портали за якими ведеться аналіз пройдених тестів';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'accounts' => MoodleAccount::paginate()
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
            Link::make('Додати')
                ->icon('icon-pencil')
                ->route('platform.moodleaccount.edit')
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
            AccountListLayout::class
        ];
    }
}