<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use App\Http\Requests\Logbook\LogbookRequest;
use App\Models\Logbook;
use App\Services\Logbook\LogbookService;
use App\Services\Profil\ProfilService;
use App\Support\WeekHelper;
use Illuminate\Http\Request;

class LogbookController extends Controller
{
    public function __construct(
        private readonly LogbookService $service,
        private readonly ProfilService $profil
    ) {
    }

    public function index(Request $request)
    {
        [$tahun, $bulan] = WeekHelper::parseBulan($request->query('bulan'));
        $profil = $this->profil->get();

        return view('logbook.index', [
            'bulan'           => sprintf('%04d-%02d', $tahun, $bulan),
            'namaBulan'       => WeekHelper::namaBulan($bulan) . ' ' . $tahun,
            'minggu'          => $this->service->weeksOfMonth($profil, $tahun, $bulan),
            'mingguMagangIni' => $this->service->currentInternshipWeek($profil),
        ]);
    }

    public function create()
    {
        return view('logbook.create', [
            'tanggalDefault' => $this->service->defaultTanggal()->toDateString(),
        ]);
    }

    public function store(LogbookRequest $request)
    {
        $logbook = $this->service->create($request->validated());

        return redirect()
            ->route('logbook.index', ['bulan' => $logbook->tanggal->format('Y-m')])
            ->with('success', 'Logbook tanggal ' . WeekHelper::tanggalIndo($logbook->tanggal) . ' berhasil disimpan.');
    }

    public function edit(Logbook $logbook)
    {
        return view('logbook.edit', ['logbook' => $logbook]);
    }

    public function update(LogbookRequest $request, Logbook $logbook)
    {
        $logbook = $this->service->update($logbook, $request->validated());

        return redirect()
            ->route('logbook.index', ['bulan' => $logbook->tanggal->format('Y-m')])
            ->with('success', 'Logbook berhasil diperbarui.');
    }

    public function destroy(Logbook $logbook)
    {
        $bulan = $logbook->tanggal->format('Y-m');
        $this->service->delete($logbook);

        return redirect()->route('logbook.index', ['bulan' => $bulan])->with('success', 'Logbook berhasil dihapus.');
    }
}