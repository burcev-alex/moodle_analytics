<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Note extends Model
{
    use AsSource;

    /**
     * @var array
     */
    protected $fillable = [
        'account_id',
        'course_id',
        'user_id',
        'quiz_id',
        'page_id',
        'question_content',
        'question_id',
        'attempt_id',
        'status'
    ];

    public function account()
    {
        return $this->hasOne(MoodleAccount::class, 'id', 'account_id');
    }


    public function course()
    {
        return $this->hasOne(MoodleCourse::class, 'id', 'course_id');
    }
}
