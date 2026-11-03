<?php

namespace App\Http\Requests\Profil;

use Illuminate\Foundation\Http\FormRequest;

class ProfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $telp = ['nullable', 'string', 'max:40', 'regex:/^[0-9+\-\s().]+$/'];

        return [
            'nama'               => ['required', 'string', 'max:120'],
            'nim'                => ['required', 'string', 'max:30'],
            'no_hp'              => $telp,
            'no_rekening_bni'    => ['nullable', 'string', 'max:40', 'regex:/^[0-9\s\-]+$/'],
            'perusahaan_nama'    => ['required', 'string', 'max:150'],
            'perusahaan_alamat'  => ['nullable', 'string', 'max:500'],
            'perusahaan_telp'    => $telp,
            'pembimbing_nama'    => ['required', 'string', 'max:150'],
            'pembimbing_jabatan' => ['nullable', 'string', 'max:150'],
            'pembimbing_hp'      => $telp,
            'kota'               => ['required', 'string', 'max:80'],
            'tanggal_mulai'      => ['required', 'date'],
            'tanggal_selesai'    => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nama'               => 'nama mahasiswa',
            'nim'                => 'NIM/NIS',
            'no_hp'              => 'no. HP mahasiswa',
            'no_rekening_bni'    => 'nomor rekening BNI',
            'perusahaan_nama'    => 'nama perusahaan',
            'perusahaan_alamat'  => 'alamat perusahaan',
            'perusahaan_telp'    => 'telepon perusahaan',
            'pembimbing_nama'    => 'nama pembimbing',
            'pembimbing_jabatan' => 'jabatan pembimbing',
            'pembimbing_hp'      => 'no. HP pembimbing',
            'kota'               => 'kota',
            'tanggal_mulai'      => 'tanggal mulai magang',
            'tanggal_selesai'    => 'tanggal selesai magang',
        ];
    }

    public function messages(): array
    {
        return [
            'required'                      => ':Attribute wajib diisi.',
            'max'                           => ':Attribute maksimal :max karakter.',
            'regex'                         => 'Format :attribute tidak valid.',
            'date'                          => ':Attribute tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ];
    }
}
