<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('question_set_id');

            $table->string('class');       // nama kelas
            $table->date('date_start');    // tanggal mulai ujian
            $table->time('time_start');    // waktu mulai
            $table->date('date_end')->nullable();   // tanggal selesai, bisa kosong
            $table->time('time_end');      // waktu selesai

            $table->timestamps();

            // relasi foreign key ke question_sets
            $table->foreign('question_set_id')
                  ->references('id')
                  ->on('question_sets')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_schedules');
    }
};
