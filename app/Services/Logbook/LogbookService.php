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
        $logbook->update($data);

        return $logbook->refresh();
    }

    public function delete(Logbook $logbook): void
    {
        $logbook->delete();
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
