<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $primaryKey = 'siswa_id';

    public $timestamps = true;

    protected $fillable = [
        'nis',
        'username',
        'password',
        'password_hint',
        'nama_siswa',
        'jenis_kelamin',
        'kelas',
        'tempat_lahir',
        'tanggal_lahir',
        'status',
        'alamat',
        'role',
        'admin_id',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }
}
