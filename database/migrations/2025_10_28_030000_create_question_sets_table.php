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
        Schema::create('question_sets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('teacher_id')->nullable();
            $table->string('subject', 120);
            $table->string('exam_type', 120);
            $table->string('semester', 60);
            $table->string('class_level', 60);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('teacher_id')
                ->references('guru_id')
                ->on('guru')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_sets');
    }
};
