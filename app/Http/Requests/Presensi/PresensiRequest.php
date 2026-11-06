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

    /** Izin/sakit tidak memakai jam datang & pulang. */
    protected function prepareForValidation(): void
    {
        if (in_array($this->input('status'), ['izin', 'sakit'], true)) {
            $this->merge(['jam_datang' => null, 'jam_pulang' => null]);
        }
    }

    public function rules(): array
    {
        $presensi = $this->route('presensi');

        return [
            'tanggal'    => [
                'required', 'date', new WeekdayOnly(),
                Rule::unique('presensis', 'tanggal')->ignore($presensi?->id),
            ],
            'status'     => ['required', Rule::in(['hadir', 'izin', 'sakit'])],
            'jam_datang' => ['required_if:status,hadir', 'nullable', 'date_format:H:i'],
            'jam_pulang' => ['nullable', 'date_format:H:i', 'after:jam_datang'],
            'keterangan' => ['nullable', 'string', 'max:150'],
        ];
    }

    public function attributes(): array
    {
        return [
            'tanggal'    => 'tanggal',
            'status'     => 'status',
            'jam_datang' => 'jam datang',
            'jam_pulang' => 'jam pulang',
            'keterangan' => 'keterangan',
        ];
    }

    public function messages(): array
    {
        return [
            'required'            => ':Attribute wajib diisi.',
            'date'                => ':Attribute tidak valid.',
            'date_format'         => 'Format :attribute harus JJ:MM (contoh 08:00).',
            'max'                 => ':Attribute maksimal :max karakter.',
            'tanggal.unique'      => 'Presensi pada tanggal ini sudah ada. Silakan ubah data yang sudah ada.',
            'status.in'           => 'Status harus Hadir, Izin, atau Sakit.',
            'jam_datang.required_if' => 'Jam datang wajib diisi bila status Hadir.',
            'jam_pulang.after'    => 'Jam pulang harus setelah jam datang.',
        ];
    }
}
