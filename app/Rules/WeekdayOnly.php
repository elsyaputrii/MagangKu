<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/** Tanggal harus hari kerja (Senin-Jumat). Sabtu & Minggu ditolak. */
class WeekdayOnly implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $tanggal = Carbon::parse((string) $value);
        } catch (\Throwable $e) {
            return; // aturan 'date' yang akan melaporkan format salah
        }

        if ($tanggal->isWeekend()) {
            $fail('Tanggal harus hari kerja (Senin–Jumat). Sabtu dan Minggu tidak dihitung.');
        }
    }
}
