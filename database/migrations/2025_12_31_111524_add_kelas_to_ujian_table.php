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
        Schema::table('ujian', function (Blueprint $table) {
            if (! Schema::hasColumn('ujian', 'kelas')) {
                $table->string('kelas', 50)->nullable()->after('durasi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ujian', function (Blueprint $table) {
            if (Schema::hasColumn('ujian', 'kelas')) {
                $table->dropColumn('kelas');
            }
        });
    }
};
