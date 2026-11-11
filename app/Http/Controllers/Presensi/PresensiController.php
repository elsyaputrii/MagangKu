<?php

namespace App\Http\Controllers\Presensi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Presensi\PresensiRequest;
use App\Models\Presensi;
use App\Services\Presensi\PresensiService;
use App\Support\WeekHelper;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function __construct(private readonly PresensiService $service)
    {
    }

    public function index(Request $request)
    {
        [$tahun, $bulan] = $this->service->parseBulan($request->query('bulan'));
        $records = $this->service->forMonth($tahun, $bulan);

        return view('presensi.index', [
            'bulan'      => sprintf('%04d-%02d', $tahun, $bulan),
            'namaBulan'  => WeekHelper::namaBulan($bulan) . ' ' . $tahun,
            'records'    => $records,
            'grid'       => $this->service->weeklyGrid($records),
            'mingguList' => $this->service->groupedWeeks($records),
            'romawi'     => WeekHelper::ROMAWI,
        ]);
    }

    public function create()
    {
        return view('presensi.create', [
            'tanggalDefault' => $this->service->defaultTanggal()->toDateString(),
        ]);
    }

    public function store(PresensiRequest $request)
    {
        $presensi = $this->service->create($request->validated());

        return redirect()
            ->route('presensi.index', ['bulan' => $presensi->tanggal->format('Y-m')])
            ->with('success', 'Presensi tanggal ' . WeekHelper::tanggalIndo($presensi->tanggal) . ' berhasil disimpan.' . $this->catatanLogbook($presensi));
    }

    public function edit(Presensi $presensi)
    {
        return view('presensi.edit', ['presensi' => $presensi]);
    }

    public function update(PresensiRequest $request, Presensi $presensi)
    {
        $presensi = $this->service->update($presensi, $request->validated());

        return redirect()
            ->route('presensi.index', ['bulan' => $presensi->tanggal->format('Y-m')])
            ->with('success', 'Presensi berhasil diperbarui.' . $this->catatanLogbook($presensi));
    }

    public function destroy(Presensi $presensi)
    {
        $bulan = $presensi->tanggal->format('Y-m');
        $this->service->delete($presensi);

        return redirect()
            ->route('presensi.index', ['bulan' => $bulan])
            ->with('success', 'Presensi berhasil dihapus.');
    }

    /** Catatan singkat tentang logbook otomatis untuk hari izin/sakit. */
    private function catatanLogbook(Presensi $presensi): string
    {
        if ($presensi->status === 'hadir') {
            return '';
        }

        return $this->service->logbookDilewati
            ? ' Logbook tanggal ini sudah Anda tulis sendiri, jadi tidak diubah.'
            : ' Logbook tanggal ini terisi otomatis.';
    }
}