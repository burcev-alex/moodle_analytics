<?php
namespace App\Orchid\Screens\MoodleAccount;

use App\Orchid\Layouts\MoodleAccountListLayout;
use App\MoodleAccount;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class MoodleAccountListScreen extends Screen
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
            Link::make('Добавить')
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
            MoodleAccountListLayout::class
        ];
    }
}