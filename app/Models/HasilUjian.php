<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilUjian extends Model
{
    use HasFactory;

    protected $table = 'hasil_ujian';
    protected $primaryKey = 'hasil_id'; 
    public $timestamps = true;
    
    // Definisikan relasi ke Ujian dan Siswa
    public function ujian()
    {
        return $this->belongsTo(Ujian::class, 'ujian_id', 'ujian_id');
    }
    
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'siswa_id'); // Asumsi ada Model Siswa
    }
}