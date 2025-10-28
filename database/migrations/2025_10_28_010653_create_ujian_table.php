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
        Schema::create('ujian', function (Blueprint $table) {
            $table->increments('ujian_id');
            $table->string('nama_ujian', 100);
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai');
            $table->unsignedSmallInteger('durasi')->comment('Durasi ujian dalam menit');
            $table->unsignedInteger('guru_id');
            $table->unsignedInteger('admin_id');
            $table->foreign('guru_id')
                ->references('guru_id')
                ->on('guru')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreign('admin_id')
                ->references('admin_id')
                ->on('admin')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ujian');
    }
};
