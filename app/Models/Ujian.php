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

    /**
     * Atribut yang dapat diisi secara massal
     * - ujian_id (int) - Primary Key
     * - namaUjian - string
     * - durasi (int) - dalam menit
     * - waktuMulai - datetime
     * - waktuSelesai - datetime
     * - kelas (string)
     */
    protected $fillable = [
        'namaUjian',
        'durasi',
        'waktuMulai',
        'waktuSelesai',
        'kelas',
        'guru_id',
        'admin_id',
        'question_set_id',
    ];

    protected $casts = [
        'waktuMulai' => 'datetime',
        'waktuSelesai' => 'datetime',
        'durasi' => 'integer',
    ];

    // Relasi ke QuestionSet (aturSoal)
    public function aturSoal()
    {
        return $this->belongsTo(QuestionSet::class, 'question_set_id', 'id');
    }
    
    // Definisikan relasi ke HasilUjian
    public function hasilUjians()
    {
        return $this->hasMany(HasilUjian::class, 'ujian_id', 'ujian_id');
    }

    /**
     * Method untuk membuat ujian baru
     */
    public static function buatUjian(array $data): self
    {
        return self::create($data);
    }

    /**
     * Method untuk melihat nilai dari ujian ini
     */
    public function lihatNilai()
    {
        return $this->hasilUjians()->get();
    }
}