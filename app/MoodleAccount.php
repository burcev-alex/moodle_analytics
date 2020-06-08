<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class MoodleAccount extends Model
{
    use AsSource;

    /**
     * @var array
     */
    protected $fillable = [
        'domain',
        'api_key',
        'endpoint',
        'full_name'
    ];
}
