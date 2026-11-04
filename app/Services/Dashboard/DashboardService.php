<?php

namespace App\Services\Dashboard;

use App\Models\Logbook;
use App\Models\Presensi;
use App\Support\WeekHelper;
use Carbon\Carbon;

class DashboardService
{
    public function summary(): array
    {
        $hariIni   = Carbon::today();
        $seninIni  = WeekHelper::weekStart($hariIni);
        $jumatIni  = WeekHelper::weekEnd($seninIni);

        $semuaPresensi = Presensi::query()->orderBy('tanggal')->get();

        $bulanIni = $semuaPresensi->filter(
            fn (Presensi $p) => $p->tanggal->format('Y-m') === $hariIni->format('Y-m')
        );

        return [
            'presensi_total'      => $semuaPresensi->count(),
            'presensi_bulan_ini'  => $bulanIni->count(),
            'presensi_kurang_jam' => $semuaPresensi->filter(fn (Presensi $p) => $p->kurang_jam)->count(),
            'presensi_belum_pulang' => $semuaPresensi->filter(
                fn (Presensi $p) => $p->jam_pulang === null && ! $p->tanggal->isSameDay($hariIni)
            )->count(),
            'presensi_hari_ini'   => $semuaPresensi->first(fn (Presensi $p) => $p->tanggal->isSameDay($hariIni)),

            'logbook_total'       => Logbook::count(),
            'logbook_minggu_ini'  => Logbook::query()
                ->whereBetween('tanggal', [$seninIni->toDateString(), $jumatIni->toDateString()])
                ->count(),
            'logbook_minggu_total' => Logbook::query()->get()
                ->map(fn (Logbook $l) => WeekHelper::weekStart($l->tanggal)->toDateString())
                ->unique()->count(),

            'presensi_terbaru'    => $semuaPresensi->sortByDesc('tanggal')->take(5)->values(),
            'logbook_terbaru'     => Logbook::query()->orderByDesc('tanggal')->limit(5)->get(),
            'bulan_ini'           => WeekHelper::namaBulan((int) $hariIni->format('n')) . ' ' . $hariIni->format('Y'),
            'minggu_ini'          => WeekHelper::tanggalIndo($seninIni) . ' – ' . WeekHelper::tanggalIndo($jumatIni),
        ];
    }
}
