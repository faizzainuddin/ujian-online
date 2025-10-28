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
        Schema::create('soal', function (Blueprint $table) {
            $table->increments('soal_id');
            $table->text('pertanyaan');
            $table->string('jawaban_benar', 255);
            $table->unsignedSmallInteger('durasi_per_soal')->default(30);
            $table->unsignedInteger('ujian_id');
            $table->foreign('ujian_id')
                ->references('ujian_id')
                ->on('ujian')
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
        Schema::dropIfExists('soal');
    }
};
