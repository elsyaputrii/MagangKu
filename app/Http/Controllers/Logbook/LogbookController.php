<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use App\Http\Requests\Logbook\LogbookRequest;
use App\Models\Logbook;
use App\Services\Logbook\LogbookService;
use App\Services\Profil\ProfilService;
use App\Support\WeekHelper;

class LogbookController extends Controller
{
    public function __construct(
        private readonly LogbookService $service,
        private readonly ProfilService $profil
    ) {
    }

    public function index()
    {
        return view('logbook.index', [
            'minggu' => $this->service->groupedByWeek($this->profil->get()),
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
            ->route('logbook.index')
            ->with('success', 'Logbook tanggal ' . WeekHelper::tanggalIndo($logbook->tanggal) . ' berhasil disimpan.');
    }

    public function edit(Logbook $logbook)
    {
        return view('logbook.edit', ['logbook' => $logbook]);
    }

    public function update(LogbookRequest $request, Logbook $logbook)
    {
        $this->service->update($logbook, $request->validated());

        return redirect()->route('logbook.index')->with('success', 'Logbook berhasil diperbarui.');
    }

    public function destroy(Logbook $logbook)
    {
        $this->service->delete($logbook);

        return redirect()->route('logbook.index')->with('success', 'Logbook berhasil dihapus.');
    }
}
