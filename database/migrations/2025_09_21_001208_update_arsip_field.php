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
        Schema::table('arsip_surats', function (Blueprint $table) {
            $table->enum('status', ['pindah', 'musnah', 'permanent', 'arsip'])->default('arsip')->after('file');
            $table->date('tgl_pindah')->nullable()->after('status');
            $table->date('tgl_musnah')->nullable()->after('tgl_pindah');
            $table->date('tgl_permanent')->nullable()->after('tgl_musnah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arsip_surats', function (Blueprint $table) {
            $table->dropColumn(['status', 'tgl_pindah', 'tgl_musnah', 'tgl_permanent']);
        });
    }
};
