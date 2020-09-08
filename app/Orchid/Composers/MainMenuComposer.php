<?php

declare(strict_types=1);

namespace App\Orchid\Composers;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemMenu;
use Orchid\Platform\Menu;

class MainMenuComposer
{
    /**
     * @var Dashboard
     */
    private $dashboard;

    /**
     * MenuComposer constructor.
     *
     * @param Dashboard $dashboard
     */
    public function __construct(Dashboard $dashboard)
    {
        $this->dashboard = $dashboard;
    }

    /**
     * Registering the main menu items.
     */
    public function compose()
    {
        // Main
        $this->dashboard->menu
            ->add(Menu::MAIN,
                ItemMenu::label('Портали')
                    ->icon('icon-layers')
                    ->route('platform.moodleaccount.list')
                    ->title('Moodle')
            )
            ->add(Menu::MAIN,
                ItemMenu::label('Курси')
                    ->icon('icon-list')
                    ->route('platform.moodlecourse.list')
            )
            ->add(Menu::MAIN,
                ItemMenu::label('LSA аналіз бази питань')
                    ->icon('icon-list')
                    ->route('platform.lsaresultcomparison.list')
                    ->title('Результат')
            )
            ->add(Menu::MAIN,
                ItemMenu::label('LSA аналіз')
                    ->icon('icon-list')
                    ->route('platform.lsa.list')
                    ->title('Очередь')
            )
            ->add(Menu::MAIN,
                ItemMenu::label('LSA аналіз (вихідні коди)')
                    ->icon('icon-list')
                    ->route('platform.lsasource.list')
            );
    }
}
