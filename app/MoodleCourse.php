<?php

namespace App;

use App\MoodleAccount;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class MoodleCourse extends Model
{
    use AsSource;

    /**
     * @var array
     */
    protected $fillable = [
        'account_id',
        'xml_id',
        'full_name'
    ];

    public function account()
    {
        return $this->hasOne(MoodleAccount::class, 'id', 'account_id');
    }
}
