<?php

namespace App\Http\Controllers\Presensi;

use App\Http\Controllers\Controller;
use App\Services\Presensi\PresensiExcelExporter;
use App\Services\Presensi\PresensiPdfExporter;
use App\Services\Presensi\PresensiService;
use App\Services\Profil\ProfilService;
use App\Support\WeekHelper;
use Illuminate\Http\Request;

class ExportPresensiController extends Controller
{
    public function __construct(
        private readonly PresensiService $presensi,
        private readonly ProfilService $profil,
        private readonly PresensiExcelExporter $excel,
        private readonly PresensiPdfExporter $pdf
    ) {
    }

    public function excel(Request $request)
    {
        [$tahun, $bulan] = $this->presensi->parseBulan($request->query('bulan'));
        $records = $this->presensi->forMonth($tahun, $bulan);

        $path = $this->excel->generate($this->profil->get(), $tahun, $bulan, $records);

        return response()
            ->download($path, $this->namaFile($tahun, $bulan, 'xlsx'))
            ->deleteFileAfterSend(true);
    }

    public function pdf(Request $request)
    {
        [$tahun, $bulan] = $this->presensi->parseBulan($request->query('bulan'));
        $grid = $this->presensi->weeklyGrid($this->presensi->forMonth($tahun, $bulan));

        return $this->pdf
            ->render($this->profil->get(), $tahun, $bulan, $grid)
            ->download($this->namaFile($tahun, $bulan, 'pdf'));
    }

    private function namaFile(int $tahun, int $bulan, string $ext): string
    {
        return sprintf('Presensi_Magang_%s_%04d-%02d.%s', WeekHelper::namaBulan($bulan), $tahun, $bulan, $ext);
    }
}
