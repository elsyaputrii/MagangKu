<?php

namespace App\Services\Logbook;

use App\Models\Logbook;
use App\Models\Profile;
use App\Support\WeekHelper;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LogbookService
{
    /**
     * Seluruh logbook dikelompokkan per minggu (Senin-Jumat), minggu terbaru di atas.
     *
     * @return Collection<int, array{nomor:int, mulai:Carbon, selesai:Carbon, items:Collection}>
     */
    public function groupedByWeek(Profile $profil): Collection
    {
        $semua = Logbook::query()->orderBy('tanggal')->get();
        $acuan = $this->tanggalAcuan($profil, $semua);

        return $semua
            ->groupBy(fn (Logbook $l) => WeekHelper::weekStart($l->tanggal)->toDateString())
            ->map(function (Collection $items, string $senin) use ($acuan) {
                $mulai = Carbon::parse($senin);

                return [
                    'nomor'   => WeekHelper::internshipWeek($mulai, $acuan),
                    'mulai'   => $mulai,
                    'selesai' => WeekHelper::weekEnd($mulai),
                    'items'   => $items->sortBy('tanggal')->values(),
                ];
            })
            ->sortByDesc(fn (array $minggu) => $minggu['mulai']->timestamp)
            ->values();
    }

    /**
     * Daftar minggu (M1, M2, ...) pada satu bulan yang MEMILIKI data logbook, terbaru di atas.
     *
     * @return Collection<int, array{kode:string, nomor_bulan:int, nomor_magang:int, senin:Carbon, mulai:Carbon, selesai:Carbon, items:Collection}>
     */
    public function weeksOfMonth(Profile $profil, int $tahun, int $bulan): Collection
    {
        $items = Logbook::query()
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('tanggal')
            ->get();

        $acuan = $this->tanggalAcuan($profil, Logbook::query()->orderBy('tanggal')->limit(1)->get());

        return $items
            ->groupBy(fn (Logbook $l) => WeekHelper::weekOfMonth($l->tanggal))
            ->map(function (Collection $grup, $nomor) use ($acuan) {
                $rentang = WeekHelper::weekRangeInMonth($grup->first()->tanggal);

                return [
                    'kode'         => 'M' . $nomor,
                    'nomor_bulan'  => (int) $nomor,
                    'nomor_magang' => WeekHelper::internshipWeek($rentang['senin'], $acuan),
                    'senin'        => $rentang['senin'],
                    'mulai'        => $rentang['mulai'],
                    'selesai'      => $rentang['selesai'],
                    'items'        => $grup->sortBy('tanggal')->values(),
                ];
            })
            ->sortByDesc(fn (array $minggu) => $minggu['senin']->timestamp)
            ->values();
    }

    /** Nomor minggu magang untuk hari ini (badge "Minggu ke-N"). */
    public function currentInternshipWeek(Profile $profil): int
    {
        $acuan = $this->tanggalAcuan($profil, Logbook::query()->orderBy('tanggal')->limit(1)->get());

        return WeekHelper::internshipWeek(Carbon::today(), $acuan);
    }

    /** Data satu minggu (untuk export), diidentifikasi dengan hari Senin. */
    public function week(Profile $profil, Carbon $senin): array
    {
        $jumat = WeekHelper::weekEnd($senin);

        $items = Logbook::query()
            ->whereBetween('tanggal', [$senin->toDateString(), $jumat->toDateString()])
            ->orderBy('tanggal')
            ->get();

        $acuan = $this->tanggalAcuan($profil, Logbook::query()->orderBy('tanggal')->limit(1)->get());

        return [
            'nomor'   => WeekHelper::internshipWeek($senin, $acuan),
            'mulai'   => $senin,
            'selesai' => $jumat,
            'items'   => $items,
        ];
    }

    public function create(array $data): Logbook
    {
        return Logbook::create($data);
    }

    public function update(Logbook $logbook, array $data): Logbook
    {
        // Setelah diedit manual, uraian dianggap tulisan sendiri (tidak ditimpa otomatis lagi)
        $data['otomatis'] = false;
        $logbook->update($data);

        return $logbook->refresh();
    }

    public function delete(Logbook $logbook): void
    {
        $logbook->delete();
    }

    /**
     * Isi logbook otomatis untuk hari izin/sakit.
     * Return false bila logbook tanggal itu sudah ditulis manual (tidak ditimpa).
     */
    public function isiOtomatisAbsen(Carbon $tanggal, string $status, ?string $keterangan): bool
    {
        $tanggalStr = $tanggal->toDateString();
        $logbook = Logbook::query()->whereDate('tanggal', $tanggalStr)->first();

        if ($logbook && ! $logbook->otomatis) {
            return false;
        }

        $data = ['uraian' => $this->uraianAbsen($status, $keterangan), 'otomatis' => true];

        if ($logbook) {
            $logbook->update($data);
        } else {
            Logbook::create($data + ['tanggal' => $tanggalStr]);
        }

        return true;
    }

    /** Hapus logbook otomatis (izin/sakit) pada tanggal tsb; logbook tulisan sendiri aman. */
    public function hapusOtomatis(Carbon $tanggal): void
    {
        Logbook::query()
            ->whereDate('tanggal', $tanggal->toDateString())
            ->where('otomatis', true)
            ->delete();
    }

    /**
     * Uraian logbook untuk hari izin/sakit.
     * - Keterangan diisi  -> dipakai apa adanya (huruf awal dibesarkan, diakhiri titik).
     * - Keterangan kosong -> kalimat standar.
     */
    public function uraianAbsen(string $status, ?string $keterangan): string
    {
        $teks = trim((string) $keterangan);

        if ($teks !== '') {
            $teks = mb_strtoupper(mb_substr($teks, 0, 1)) . mb_substr($teks, 1);

            return preg_match('/[.!?]$/u', $teks) ? $teks : $teks . '.';
        }

        return $status === 'sakit'
            ? 'Tidak masuk magang karena sakit.'
            : 'Izin tidak masuk magang.';
    }

    public function defaultTanggal(): Carbon
    {
        $hariIni = Carbon::today();

        return $hariIni->isWeekend() ? $hariIni->copy()->previousWeekday() : $hariIni;
    }

    /** Tanggal acuan minggu ke-1: tanggal mulai magang di profil, atau logbook paling awal. */
    private function tanggalAcuan(Profile $profil, Collection $semua): Carbon
    {
        if ($profil->tanggal_mulai) {
            return Carbon::parse($profil->tanggal_mulai->format('Y-m-d'));
        }

        $pertama = $semua->first();

        return $pertama ? Carbon::parse($pertama->tanggal->format('Y-m-d')) : Carbon::today();
    }
}