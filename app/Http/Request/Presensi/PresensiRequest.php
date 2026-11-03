<?php

namespace App\Http\Requests\Presensi;

use App\Rules\WeekdayOnly;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PresensiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $presensi = $this->route('presensi');

        return [
            'tanggal'    => [
                'required', 'date', new WeekdayOnly(),
                Rule::unique('presensis', 'tanggal')->ignore($presensi?->id),
            ],
            'jam_datang' => ['required', 'date_format:H:i'],
            'jam_pulang' => ['nullable', 'date_format:H:i', 'after:jam_datang'],
        ];
    }

    public function attributes(): array
    {
        return [
            'tanggal'    => 'tanggal',
            'jam_datang' => 'jam datang',
            'jam_pulang' => 'jam pulang',
        ];
    }

    public function messages(): array
    {
        return [
            'required'            => ':Attribute wajib diisi.',
            'date'                => ':Attribute tidak valid.',
            'date_format'         => 'Format :attribute harus JJ:MM (contoh 08:00).',
            'tanggal.unique'      => 'Presensi pada tanggal ini sudah ada. Silakan ubah data yang sudah ada.',
            'jam_pulang.after'    => 'Jam pulang harus setelah jam datang.',
        ];
    }
}
