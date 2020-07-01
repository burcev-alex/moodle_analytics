<?php
namespace App\Orchid\Screens\ResultComparison;

use App\Orchid\Layouts\ResultComparison\LsaListLayout;
use App\LsaResultComparison;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class LsaListScreen extends Screen
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
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'results' => LsaResultComparison::paginate()
        ];
    }

    /**
     * Button commands.
     *
     * @return Link[]
     */
    public function commandBar(): array
    {
        return [];
    }

    /**
     * Views.
     *
     * @return Layout[]
     */
    public function layout(): array
    {
        return [
            LsaListLayout::class
        ];
    }
}