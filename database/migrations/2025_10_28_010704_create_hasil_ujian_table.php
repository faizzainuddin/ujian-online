<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hasil_ujian', function (Blueprint $table) {
            $table->increments('hasil_id');
            $table->unsignedInteger('ujian_id');
            $table->unsignedInteger('siswa_id');
            $table->decimal('nilai', 5, 2)->nullable();
            $table->enum('status', ['Belum Mulai', 'Sedang Dikerjakan', 'Selesai'])->default('Belum Mulai');
            $table->dateTime('waktu_mulai')->nullable();
            $table->dateTime('waktu_selesai')->nullable();
            $table->foreign('ujian_id')
                ->references('ujian_id')
                ->on('ujian')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreign('siswa_id')
                ->references('siswa_id')
                ->on('siswa')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_ujian');
    }
};
