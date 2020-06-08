<?php

namespace App\Orchid\Layouts\Moodle;

use App\MoodleAccount;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;

class AccountListLayout extends Table
{
    /**
     * Data source.
     *
     * @var string
     */
    public $target = 'accounts';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::set('full_name', 'Название портала')
                ->render(function (MoodleAccount $user) {
                    return Link::make($user->full_name)
                        ->route('platform.moodleaccount.edit', $user);
                }),
            
            TD::set('domain', 'Домен'),
        ];
    }
}