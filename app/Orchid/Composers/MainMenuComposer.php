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
                ItemMenu::label('Moodle account')
                    ->icon('icon-layers')
                    ->route('platform.moodleaccount.list')
                    ->title('Moodle')
            )/*
            ->add(Menu::MAIN,
                ItemMenu::label('Log')
                    ->icon('icon-list')
                    ->route('platform.example.editors')
            )*/;
    }
}
