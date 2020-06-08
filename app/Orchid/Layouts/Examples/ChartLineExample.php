<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Examples;

use Orchid\Screen\Layouts\Chart;

class ChartLineExample extends Chart
{
    /**
     * @var string
     */
    protected $title = 'Line Chart';

    /**
     * @var int
     */
    protected $height = 250;

    /**
     * Available options:
     * 'bar', 'line',
     * 'pie', 'percentage'.
     *
     * @var string
     */
    protected $type = 'line';

    /**
     * @var array
     */
    protected $labels = [
        '12am-3am',
        '3am-6am',
        '6am-9am',
        '9am-12pm',
        '12pm-3pm',
        '3pm-6pm',
        '6pm-9pm',
    ];

    /**
     * @var string
     */
    protected $target = 'charts';
}
