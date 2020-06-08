<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use App\MoodleAccount;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Relation;

class MoodleAccountFilter extends Filter
{
    /**
     * @var array
     */
    public $parameters = [
        'account_id',
    ];

    /**
     * @return string
     */
    public function name(): string
    {
        return 'Moodle account';
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function run(Builder $builder): Builder
    {
        return $builder->whereHas('account', function (Builder $query) {
            $query->where('account_id', $this->request->get('account_id'));
        });
    }

    /**
     * @return Field[]
     */
    public function display(): array
    {
        return [
            Relation::make('account_id')
                    ->title('Moodle account')
                    ->fromModel(MoodleUser::class, 'full_name')
                    ->empty()
                    ->value($this->request->get('account_id')),
        ];
    }
}
