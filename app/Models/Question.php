<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_set_id',
        'prompt',
        'options',
        'answer_index',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function questionSet()
    {
        return $this->belongsTo(QuestionSet::class);
    }
}
