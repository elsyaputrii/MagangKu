<?php

namespace App\Support;

use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Logika pengelompokan hari/minggu yang dipakai bersama oleh Presensi & Logbook.
 * Hari kerja = Senin-Jumat. Sabtu & Minggu tidak dihitung.
 */
class WeekHelper
{
    public const BULAN = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public const HARI = [
        1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu',
    ];

    public const ROMAWI = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V'];

    public static function namaBulan(int $bulan): string
    {
        return self::BULAN[$bulan] ?? '';
    }

    public static function namaHari(CarbonInterface $tanggal): string
    {
        return self::HARI[$tanggal->dayOfWeekIso] ?? '';
    }

    /** Contoh: "02 Oktober 2026". */
    public static function tanggalIndo(CarbonInterface $tanggal): string
    {
        return $tanggal->format('d') . ' ' . self::namaBulan((int) $tanggal->format('n')) . ' ' . $tanggal->format('Y');
    }

    /** Contoh: "28-09-2026". */
    public static function tanggalStrip(CarbonInterface $tanggal): string
    {
        return $tanggal->format('d-m-Y');
    }

    /** Contoh: "28 / 09 / 2026" (gaya logbook template). */
    public static function tanggalLogbook(CarbonInterface $tanggal): string
    {
        return $tanggal->format('d / m / Y');
    }

    /** Hari Senin pada minggu dari tanggal tsb. */
    public static function weekStart(CarbonInterface $tanggal): Carbon
    {
        return Carbon::parse($tanggal->format('Y-m-d'))->startOfWeek(Carbon::MONDAY)->startOfDay();
    }

    /** Hari Jumat pada minggu dari Senin yang diberikan. */
    public static function weekEnd(CarbonInterface $senin): Carbon
    {
        return Carbon::parse($senin->format('Y-m-d'))->addDays(4)->startOfDay();
    }

    /** Hari kerja pertama (Senin-Jumat) pada bulan tsb. */
    public static function firstWorkday(int $tahun, int $bulan): Carbon
    {
        $tanggal = Carbon::create($tahun, $bulan, 1)->startOfDay();
        while ($tanggal->isWeekend()) {
            $tanggal->addDay();
        }

        return $tanggal;
    }

    /**
     * Minggu ke-berapa (1-5 => I-V) dalam bulan.
     * Minggu = Senin-Jumat. Minggu I adalah minggu yang memuat hari kerja pertama bulan itu.
     */
    public static function weekOfMonth(CarbonInterface $tanggal): int
    {
        $pertama = self::firstWorkday((int) $tanggal->format('Y'), (int) $tanggal->format('n'));

        return self::weeksBetween(self::weekStart($pertama), self::weekStart($tanggal)) + 1;
    }

    /** Nomor minggu magang (minggu ke-N sejak tanggal mulai magang). */
    public static function internshipWeek(CarbonInterface $tanggal, CarbonInterface $mulai): int
    {
        return max(1, self::weeksBetween(self::weekStart($mulai), self::weekStart($tanggal)) + 1);
    }

    public static function weeksBetween(CarbonInterface $a, CarbonInterface $b): int
    {
        return (int) round(($b->getTimestamp() - $a->getTimestamp()) / 604800);
    }
}
