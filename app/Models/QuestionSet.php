<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionSet extends Model
{
    use HasFactory;

    protected $table = 'question_sets';
    protected $primaryKey = 'id'; 
    public $timestamps = true;
    
    // Definisikan relasi ke Ujian (asumsi kolom question_set_id ada di tabel ujian)
    public function ujians()
    {
        return $this->hasMany(Ujian::class, 'question_set_id', 'id');
    }
}