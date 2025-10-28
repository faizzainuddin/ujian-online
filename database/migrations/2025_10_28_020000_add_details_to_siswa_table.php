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
        Schema::table('siswa', function (Blueprint $table) {
            $table->string('nis', 20)->unique()->after('siswa_id');
            $table->string('password_hint', 100)->nullable()->after('password');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable()->after('nama_siswa');
            $table->string('tempat_lahir', 100)->nullable()->after('jenis_kelamin');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif')->after('kelas');
            $table->string('alamat', 255)->nullable()->after('status');
            $table->string('role', 30)->default('Siswa')->after('alamat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropColumn([
                'nis',
                'jenis_kelamin',
                'password_hint',
                'tempat_lahir',
                'tanggal_lahir',
                'status',
                'alamat',
                'role',
            ]);
        });
    }
};
