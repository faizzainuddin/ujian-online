<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSchedule extends Model
{
    protected $fillable = [
        'question_set_id',
        'class',
        'date_start',
        'time_start',
        'date_end',
        'time_end',
    ];

    protected $casts = [
        'date_start' => 'datetime',
        'date_end' => 'datetime',
    ];

    public function questionSet()
    {
        return $this->belongsTo(QuestionSet::class, 'question_set_id');
    }
}
