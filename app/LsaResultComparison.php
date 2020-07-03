<?php

namespace App;

use App\MoodleAccount;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class LsaResultComparison extends Model
{
    use AsSource;

    /**
     * @var array
     */
    protected $fillable = [
        'account_id',
        'course_id',
        'question_id',
        'question_content',
        'page_id',
        'page_content',
        'params',
        'status'
    ];

    public function account()
    {
        return $this->hasOne(MoodleAccount::class, 'id', 'account_id');
    }

    public function course()
    {
        return $this->hasOne(MoodleCourse::class, 'xml_id', 'course_id');
    }

    public function scopeCourseFullName($query)
    {
        $query->addSubSelect('course_full_name', MoodleCourse::select('full_name')
            ->whereColumn('xml_id', 'users.course_id')
            ->latest()
        );
    }
}
