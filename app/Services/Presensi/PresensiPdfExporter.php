<?php

namespace App\Services\Presensi;

use App\Models\Profile;
use App\Support\Logo;
use App\Support\WeekHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;

class PresensiPdfExporter
{
    public function render(Profile $profil, int $tahun, int $bulan, array $grid): DomPdf
    {
        return Pdf::loadView('exports.presensi-pdf', [
            'profil'    => $profil,
            'tahun'     => $tahun,
            'namaBulan' => WeekHelper::namaBulan($bulan),
            'grid'      => $grid,
            'romawi'    => WeekHelper::ROMAWI,
            'logo'      => Logo::dataUri(),
            'minJam'    => config('magang.min_jam_kerja'),
        ])->setPaper('a4', 'landscape');
    }
}
