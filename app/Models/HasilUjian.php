<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * HasilUjian Model - Following SOLID Principles
 * 
 * Single Responsibility: Handle exam result data persistence
 * Open/Closed: Extensible through relationships and scopes
 * Interface Segregation: Clean model interface for exam results
 * Dependency Inversion: Depends on Laravel's Eloquent abstraction
 */
class HasilUjian extends Model
{
    use HasFactory;

    // Table configuration following Laravel conventions
    protected $table = 'hasil_ujian';
    protected $primaryKey = 'hasil_id'; 
    public $timestamps = true;
    
    /**
     * The attributes that are mass assignable.
     * Security: Only allow necessary fields for mass assignment
     */
    protected $fillable = [
        'siswa_id',
        'ujian_id', 
        'nilai',
        'status',
        'waktu_mulai',
        'waktu_selesai'
    ];

    /**
     * The attributes that should be cast to native types.
     * Type safety: Ensure proper data types
     */
    protected $casts = [
        'nilai' => 'decimal:2',
        'waktu_selesai' => 'datetime',
        'waktu_mulai' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: HasilUjian belongs to Ujian
     * Single Responsibility: Define relationship contract
     */
    public function ujian()
    {
        return $this->belongsTo(Ujian::class, 'ujian_id', 'ujian_id');
    }
    
    /**
     * Relationship: HasilUjian belongs to Siswa  
     * Single Responsibility: Define relationship contract
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'siswa_id');
    }

    /**
     * Scope: Get passing results (nilai >= 75)
     * Open/Closed: Extensible query scopes
     */
    public function scopePassed($query)
    {
        return $query->where('nilai', '>=', 75);
    }

    /**
     * Scope: Get failed results (nilai < 75)
     * Open/Closed: Extensible query scopes  
     */
    public function scopeFailed($query)
    {
        return $query->where('nilai', '<', 75);
    }

    /**
     * Accessor: Get formatted score with percentage
     * Interface Segregation: Clean data presentation
     */
    public function getFormattedScoreAttribute()
    {
        return $this->nilai . '/100';
    }

    /**
     * Accessor: Get pass/fail status
     * Single Responsibility: Determine pass status
     */
    public function getStatusTextAttribute()
    {
        return $this->nilai >= 75 ? 'Lulus' : 'Tidak Lulus';
    }

    /**
     * Accessor: Get status CSS class for styling
     * Single Responsibility: UI helper for status display
     */
    public function getStatusClassAttribute()
    {
        return $this->nilai >= 75 ? 'lulus' : 'tidak-lulus';
    }
}