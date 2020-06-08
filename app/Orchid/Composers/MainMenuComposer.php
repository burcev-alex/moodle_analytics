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
                ItemMenu::label('Результирующая матрица')
                    ->icon('icon-list')
                    ->route('platform.matrix.list')
            )
            ->add(Menu::MAIN,
                ItemMenu::label('Moodle Account')
                    ->icon('icon-list')
                    ->route('platform.moodleaccount.list')
            )
            ->add(Menu::MAIN,
                ItemMenu::label('Очередь анализа')
                    ->icon('icon-list')
                    ->route('platform.analyze.list')
            );
    }
}
