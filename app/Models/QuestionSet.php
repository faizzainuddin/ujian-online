<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'subject',
        'exam_type',
        'semester',
        'class_level',
        'description',
    ];

    public function teacher()
    {
        return $this->belongsTo(Guru::class, 'teacher_id', 'guru_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function examSchedules()
    {
        return $this->hasMany(ExamSchedule::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->exam_type} {$this->semester} - {$this->class_level} - {$this->subject}";
    }

}
