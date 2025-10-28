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
        Schema::create('siswa', function (Blueprint $table) {
            $table->increments('siswa_id');
            $table->string('username', 50)->unique();
            $table->string('password', 100);
            $table->string('nama_siswa', 100);
            $table->string('kelas', 50);
            $table->unsignedInteger('admin_id');
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
        Schema::dropIfExists('siswa');
    }
};
