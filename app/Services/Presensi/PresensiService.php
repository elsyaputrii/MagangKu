<?php

namespace App\Services\Presensi;

use App\Models\Presensi;
use App\Services\Logbook\LogbookService;
use App\Support\WeekHelper;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PresensiService
{
    /** True bila logbook tanggal itu sudah ditulis manual sehingga tidak ditimpa otomatis. */
    public bool $logbookDilewati = false;

    public function __construct(private readonly LogbookService $logbook)
    {
    }

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
        $presensi = Presensi::create($this->normalisasi($data));
        $this->sinkronLogbook($presensi);

        return $presensi;
    }

    public function update(Presensi $presensi, array $data): Presensi
    {
        $tanggalLama = $presensi->tanggal->copy();

        $presensi->update($this->normalisasi($data));
        $presensi->refresh();

        // Jika tanggal dipindah, logbook otomatis di tanggal lama ikut dibersihkan
        if (! $tanggalLama->isSameDay($presensi->tanggal)) {
            $this->logbook->hapusOtomatis($tanggalLama);
        }

        $this->sinkronLogbook($presensi);

        return $presensi;
    }

    public function delete(Presensi $presensi): void
    {
        $this->logbook->hapusOtomatis($presensi->tanggal);
        $presensi->delete();
    }

    /** Tanggal awal untuk form tambah: hari ini, atau Jumat terakhir bila akhir pekan. */
    public function defaultTanggal(): Carbon
    {
        $hariIni = Carbon::today();

        return $hariIni->isWeekend() ? $hariIni->copy()->previousWeekday() : $hariIni;
    }

    /** Rapikan data sesuai status: hadir tanpa keterangan; izin/sakit tanpa jam. */
    private function normalisasi(array $data): array
    {
        $data['status'] = $data['status'] ?? 'hadir';

        if ($data['status'] === 'hadir') {
            $data['keterangan'] = null;
        } else {
            $data['jam_datang'] = null;
            $data['jam_pulang'] = null;
            $data['keterangan'] = isset($data['keterangan']) ? trim((string) $data['keterangan']) ?: null : null;
        }

        return $data;
    }

    /** Izin/sakit -> isi logbook otomatis. Hadir -> hapus logbook otomatis (jika ada). */
    private function sinkronLogbook(Presensi $presensi): void
    {
        $this->logbookDilewati = false;

        if ($presensi->status === 'hadir') {
            $this->logbook->hapusOtomatis($presensi->tanggal);

            return;
        }

        $this->logbookDilewati = ! $this->logbook->isiOtomatisAbsen(
            $presensi->tanggal,
            $presensi->status,
            $presensi->keterangan
        );
    }
}
