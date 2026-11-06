<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            $table->string('status', 10)->default('hadir')->after('tanggal'); // hadir | izin | sakit
            $table->string('keterangan', 150)->nullable()->after('jam_pulang');
        });

        // Izin/sakit tidak punya jam datang
        Schema::table('presensis', function (Blueprint $table) {
            $table->time('jam_datang')->nullable()->change();
        });

        Schema::table('logbooks', function (Blueprint $table) {
            // true = uraian dibuat otomatis dari presensi izin/sakit
            $table->boolean('otomatis')->default(false)->after('uraian');
        });
    }

    public function down(): void
    {
        Schema::table('logbooks', function (Blueprint $table) {
            $table->dropColumn('otomatis');
        });

        Schema::table('presensis', function (Blueprint $table) {
            $table->dropColumn(['status', 'keterangan']);
        });
    }
};
