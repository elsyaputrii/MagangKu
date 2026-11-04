<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            // Identitas mahasiswa
            $table->string('nama', 120);
            $table->string('nim', 30);
            $table->string('no_hp', 30)->nullable();
            $table->string('no_rekening_bni', 40)->nullable();
            // Identitas perusahaan
            $table->string('perusahaan_nama', 150);
            $table->text('perusahaan_alamat')->nullable();
            $table->string('perusahaan_telp', 40)->nullable();
            // Pembimbing di perusahaan
            $table->string('pembimbing_nama', 150);
            $table->string('pembimbing_jabatan', 150)->nullable();
            $table->string('pembimbing_hp', 40)->nullable();
            // Pengaturan magang
            $table->string('kota', 80)->default('Batam');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
