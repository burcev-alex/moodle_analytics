<?php

namespace App\Orchid\Screens;

use App\Orchid\Filters\MoodleAccountFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class MatrixFiltersLayout extends Selection
{
    /**
     * @return Filter[]
     */
    public function filters(): array
    {
        return [
            MoodleAccountFilter::class,
        ];
    }
}
