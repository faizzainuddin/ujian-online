<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'admin';

    protected $primaryKey = 'admin_id';

    public $timestamps = true;

    protected $fillable = [
        'username',
        'password',
        'nama_admin',
    ];

    protected $hidden = ['password'];

    /**
     * Set the password attribute while ensuring it is hashed.
     */
    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }
}
