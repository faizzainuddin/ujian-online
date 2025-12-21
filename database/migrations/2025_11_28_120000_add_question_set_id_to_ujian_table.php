<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom relasi ke question_sets agar fitur nilai siswa bisa join.
     */
    public function up(): void
    {
        Schema::table('ujian', function (Blueprint $table) {
            if (! Schema::hasColumn('ujian', 'question_set_id')) {
                $table->unsignedBigInteger('question_set_id')->nullable()->after('nama_ujian');
                $table->foreign('question_set_id')
                    ->references('id')
                    ->on('question_sets')
                    ->cascadeOnUpdate()
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('ujian', function (Blueprint $table) {
            if (Schema::hasColumn('ujian', 'question_set_id')) {
                $table->dropForeign(['question_set_id']);
                $table->dropColumn('question_set_id');
            }
        });
    }
};
