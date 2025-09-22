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
        Schema::table('surat_masuks', function (Blueprint $table) {
            $table->enum('riwayat', ['pindah', 'musnah', 'permanent', 'arsip'])->default('arsip')->after('status_arsip');
            $table->date('tgl_pindah')->nullable()->after('riwayat');
            $table->date('tgl_musnah')->nullable()->after('tgl_pindah');
            $table->date('tgl_permanent')->nullable()->after('tgl_musnah');
        });
        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->enum('riwayat', ['pindah', 'musnah', 'permanent', 'arsip'])->default('arsip')->after('status_arsip');
            $table->date('tgl_pindah')->nullable()->after('riwayat');
            $table->date('tgl_musnah')->nullable()->after('tgl_pindah');
            $table->date('tgl_permanent')->nullable()->after('tgl_musnah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_masuks', function (Blueprint $table) {
            $table->dropColumn(['riwayat', 'tgl_pindah', 'tgl_musnah', 'tgl_permanent']);
        });
        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->dropColumn(['riwayat', 'tgl_pindah', 'tgl_musnah', 'tgl_permanent']);
        });
    }
};
