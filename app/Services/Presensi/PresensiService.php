<?php

namespace App\Services\Presensi;

use App\Models\Presensi;
use App\Support\WeekHelper;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PresensiService
{
    /** Ubah "YYYY-MM" menjadi [tahun, bulan]; default bulan berjalan. */
    public function parseBulan(?string $bulan): array
    {
        if ($bulan && preg_match('/^(\d{4})-(0[1-9]|1[0-2])$/', $bulan, $m)) {
            return [(int) $m[1], (int) $m[2]];
        }

        $sekarang = Carbon::now();

        return [(int) $sekarang->format('Y'), (int) $sekarang->format('n')];
    }

    public function forMonth(int $tahun, int $bulan): Collection
    {
        return Presensi::query()->bulan($tahun, $bulan)->orderBy('tanggal')->get();
    }

    /**
     * Kelompokkan presensi menjadi grid minggu I-V x hari Senin-Jumat.
     * Hasil: [1..5][0..4] => Presensi|null  (0 = Senin ... 4 = Jumat)
     */
    public function weeklyGrid(Collection $records): array
    {
        $grid = [];
        foreach (range(1, 5) as $minggu) {
            $grid[$minggu] = array_fill(0, 5, null);
        }

        foreach ($records as $presensi) {
            $minggu = WeekHelper::weekOfMonth($presensi->tanggal);
            $hari   = $presensi->tanggal->dayOfWeekIso - 1;

            if ($hari >= 0 && $hari <= 4 && $minggu >= 1 && $minggu <= 5) {
                $grid[$minggu][$hari] = $presensi;
            }
        }

        return $grid;
    }

    public function create(array $data): Presensi
    {
        return Presensi::create($data);
    }

    public function update(Presensi $presensi, array $data): Presensi
    {
        $presensi->update($data);

        return $presensi->refresh();
    }

    public function delete(Presensi $presensi): void
    {
        $presensi->delete();
    }

    /** Tanggal awal untuk form tambah: hari ini, atau Jumat terakhir bila akhir pekan. */
    public function defaultTanggal(): Carbon
    {
        $hariIni = Carbon::today();

        return $hariIni->isWeekend() ? $hariIni->copy()->previousWeekday() : $hariIni;
    }
}
