<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    use HasFactory;

    protected $table = 'ujian';
    protected $primaryKey = 'ujian_id';
    public $timestamps = true;

    // Definisikan relasi ke QuestionSet (asumsi kolom question_set_id ada di tabel ujian)
    public function questionSet()
    {
        return $this->belongsTo(QuestionSet::class, 'question_set_id', 'id');
    }
    
    // Definisikan relasi ke HasilUjian
    public function hasilUjians()
    {
        return $this->hasMany(HasilUjian::class, 'ujian_id', 'ujian_id');
    }
}