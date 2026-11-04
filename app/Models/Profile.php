<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'nama', 'nim', 'no_hp', 'no_rekening_bni',
        'perusahaan_nama', 'perusahaan_alamat', 'perusahaan_telp',
        'pembimbing_nama', 'pembimbing_jabatan', 'pembimbing_hp',
        'kota', 'tanggal_mulai', 'tanggal_selesai',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * Aplikasi ini single-user: hanya ada satu baris profil.
     * Jika belum ada, dibuat otomatis dengan nilai awal agar export tidak error.
     */
    public static function current(): self
    {
        return static::query()->first() ?? static::create([
            'nama'            => 'Nama Mahasiswa',
            'nim'             => '-',
            'perusahaan_nama' => 'Nama Perusahaan',
            'pembimbing_nama' => 'Nama Pembimbing',
            'kota'            => config('magang.kota_default'),
        ]);
    }
}
