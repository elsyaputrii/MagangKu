<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Mengisi profil awal sesuai contoh pada template (dapat diubah di menu Profil).
     */
    public function run(): void
    {
        Profile::query()->firstOrCreate([], [
            'nama'               => 'Elsya Ananda Putri',
            'nim'                => '3312411089',
            'no_hp'              => '085762028203',
            'no_rekening_bni'    => null,
            'perusahaan_nama'    => 'Politeknik Negeri Batam',
            'perusahaan_alamat'  => 'Jl. Ahmad Yani Batam Kota. Kota Batam, Kepulauan Riau, Indonesia.',
            'perusahaan_telp'    => '+62-778-469858',
            'pembimbing_nama'    => 'Evaliata BR.Sembiring, S.Kom, M.Cs',
            'pembimbing_jabatan' => 'Pembimbing Magang serta Kepala Unit P4M',
            'pembimbing_hp'      => '+62 813-6405-5215',
            'kota'               => 'Batam',
            'tanggal_mulai'      => '2026-09-07',
            'tanggal_selesai'    => null,
        ]);
    }
}
