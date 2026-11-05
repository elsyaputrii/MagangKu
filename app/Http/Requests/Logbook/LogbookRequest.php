<?php

namespace App\Http\Requests\Logbook;

use App\Rules\WeekdayOnly;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LogbookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $logbook = $this->route('logbook');

        return [
            'tanggal' => [
                'required', 'date', new WeekdayOnly(),
                Rule::unique('logbooks', 'tanggal')->ignore($logbook?->id),
            ],
            'uraian'  => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'tanggal' => 'tanggal',
            'uraian'  => 'uraian kegiatan',
        ];
    }

    public function messages(): array
    {
        return [
            'required'       => ':Attribute wajib diisi.',
            'date'           => ':Attribute tidak valid.',
            'tanggal.unique' => 'Logbook pada tanggal ini sudah ada. Silakan ubah data yang sudah ada.',
            'uraian.min'     => 'Uraian kegiatan minimal :min karakter.',
            'uraian.max'     => 'Uraian kegiatan maksimal :max karakter.',
        ];
    }
}
