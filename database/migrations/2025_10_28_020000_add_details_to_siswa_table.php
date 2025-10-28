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
            if (! Schema::hasColumn('siswa', 'nis')) {
                $table->string('nis', 20)->nullable();
            }

            if (! Schema::hasColumn('siswa', 'password_hint')) {
                $table->string('password_hint', 100)->nullable();
            }

            if (! Schema::hasColumn('siswa', 'jenis_kelamin')) {
                $table->string('jenis_kelamin', 20)->nullable();
            }

            if (! Schema::hasColumn('siswa', 'tempat_lahir')) {
                $table->string('tempat_lahir', 100)->nullable();
            }

            if (! Schema::hasColumn('siswa', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable();
            }

            if (! Schema::hasColumn('siswa', 'status')) {
                $table->string('status', 10)->default('Aktif');
            }

            if (! Schema::hasColumn('siswa', 'alamat')) {
                $table->string('alamat', 255)->nullable();
            }

            if (! Schema::hasColumn('siswa', 'role')) {
                $table->string('role', 30)->default('Siswa');
            }
        });

        Schema::table('siswa', function (Blueprint $table) {
            if (! Schema::hasColumn('siswa', 'nis')) {
                return;
            }

            $table->unique('nis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            if (Schema::hasColumn('siswa', 'nis')) {
                $table->dropUnique('siswa_nis_unique');
            }

            $columns = [
                'nis',
                'jenis_kelamin',
                'password_hint',
                'tempat_lahir',
                'tanggal_lahir',
                'status',
                'alamat',
                'role',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('siswa', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
