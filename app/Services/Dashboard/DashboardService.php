<?php

namespace App\Services\Dashboard;

use App\Models\Logbook;
use App\Models\Presensi;
use App\Support\WeekHelper;
use Carbon\Carbon;

class DashboardService
{
    /**
     * @param string|null $bulan  "YYYY-MM" (default: bulan berjalan)
     * @param int|null    $minggu 1-5 (M1-M5). Default: minggu berjalan bila bulan = bulan ini, selain itu M1.
     */
    public function summary(?string $bulan = null, ?int $minggu = null): array
    {
        $hariIni  = Carbon::today();
        $seninIni = WeekHelper::weekStart($hariIni);
        $jumatIni = WeekHelper::weekEnd($seninIni);

        $semuaPresensi = Presensi::query()->orderBy('tanggal')->get();

        $bulanIni = $semuaPresensi->filter(
            fn (Presensi $p) => $p->tanggal->format('Y-m') === $hariIni->format('Y-m')
        );

        $filter = $this->filterMinggu($bulan, $minggu, $hariIni);

        $presensiMinggu = $semuaPresensi
            ->filter(fn (Presensi $p) => $p->tanggal->between($filter['senin'], $filter['jumat'])
                && $p->tanggal->format('Y-m') === $filter['bulan'])
            ->sortBy('tanggal')
            ->values();

        $logbookMinggu = Logbook::query()
            ->whereBetween('tanggal', [$filter['senin']->toDateString(), $filter['jumat']->toDateString()])
            ->whereYear('tanggal', $filter['tahun'])
            ->whereMonth('tanggal', $filter['bulan_angka'])
            ->orderBy('tanggal')
            ->get();

        return [
            'presensi_total'        => $semuaPresensi->count(),
            'presensi_bulan_ini'    => $bulanIni->count(),
            'presensi_izin_sakit'   => $semuaPresensi->filter(fn (Presensi $p) => ! $p->hadir)->count(),
            'presensi_kurang_jam'   => $semuaPresensi->filter(fn (Presensi $p) => $p->kurang_jam)->count(),
            'presensi_belum_pulang' => $semuaPresensi->filter(
                fn (Presensi $p) => $p->hadir && $p->jam_pulang === null && ! $p->tanggal->isSameDay($hariIni)
            )->count(),

            'logbook_total'        => Logbook::count(),
            'logbook_minggu_ini'   => Logbook::query()
                ->whereBetween('tanggal', [$seninIni->toDateString(), $jumatIni->toDateString()])
                ->count(),
            'logbook_minggu_total' => Logbook::query()->get()
                ->map(fn (Logbook $l) => WeekHelper::weekStart($l->tanggal)->toDateString())
                ->unique()->count(),

            // Dua daftar di Dashboard: hanya untuk minggu yang dipilih (default: minggu ini)
            'presensi_minggu' => $presensiMinggu,
            'logbook_minggu'  => $logbookMinggu,
            'filter'          => $filter,

            'bulan_ini'  => WeekHelper::namaBulan((int) $hariIni->format('n')) . ' ' . $hariIni->format('Y'),
            'minggu_ini' => WeekHelper::tanggalIndo($seninIni) . ' – ' . WeekHelper::tanggalIndo($jumatIni),
        ];
    }

    /** Hitung rentang Senin–Jumat untuk bulan + M1..M5 yang dipilih, beserta daftar pilihannya. */
    private function filterMinggu(?string $bulan, ?int $minggu, Carbon $hariIni): array
    {
        if ($bulan && preg_match('/^(\d{4})-(0[1-9]|1[0-2])$/', $bulan, $m)) {
            $tahun = (int) $m[1];
            $bln   = (int) $m[2];
        } else {
            $tahun = (int) $hariIni->format('Y');
            $bln   = (int) $hariIni->format('n');
        }

        $kode        = sprintf('%04d-%02d', $tahun, $bln);
        $bulanBerjalan = $kode === $hariIni->format('Y-m');

        $awalBulan  = Carbon::create($tahun, $bln, 1)->startOfDay();
        $akhirBulan = $awalBulan->copy()->endOfMonth()->startOfDay();
        $senin1     = WeekHelper::weekStart(WeekHelper::firstWorkday($tahun, $bln));

        // Pilihan minggu: hanya yang benar-benar ada pada bulan tsb
        $pilihan = [];
        foreach (range(1, 5) as $n) {
            $seninN = $senin1->copy()->addWeeks($n - 1);
            if ($seninN->gt($akhirBulan)) {
                continue;
            }
            $mulai   = $seninN->lt($awalBulan) ? $awalBulan : $seninN;
            $selesai = WeekHelper::weekEnd($seninN);
            if ($selesai->gt($akhirBulan)) {
                $selesai = $akhirBulan;
            }
            $pilihan[$n] = sprintf(
                'M%d · %s – %s %s',
                $n,
                $mulai->format('d'),
                $selesai->format('d'),
                substr(WeekHelper::namaBulan($bln), 0, 3)
            );
        }

        $default = $bulanBerjalan ? min(5, WeekHelper::weekOfMonth($hariIni)) : 1;
        $dipilih = ($minggu !== null && isset($pilihan[$minggu]))
            ? $minggu
            : (isset($pilihan[$default]) ? $default : 1);

        $senin = $senin1->copy()->addWeeks($dipilih - 1);
        $jumat = WeekHelper::weekEnd($senin);

        $mingguIni = $bulanBerjalan
            && $hariIni->between($senin, $jumat->copy()->addDays(2)); // Sabtu/Minggu masih dihitung minggu yang sama

        return [
            'tahun'       => $tahun,
            'bulan_angka' => $bln,
            'bulan'       => $kode,
            'nama_bulan'  => WeekHelper::namaBulan($bln) . ' ' . $tahun,
            'minggu'      => $dipilih,
            'senin'       => $senin,
            'jumat'       => $jumat,
            'rentang'     => WeekHelper::tanggalIndo($senin) . ' – ' . WeekHelper::tanggalIndo($jumat),
            'pilihan'     => $pilihan,
            'minggu_ini'  => $mingguIni,
        ];
    }
}
