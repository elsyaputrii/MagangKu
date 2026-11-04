<?php

namespace App\Services\Logbook;

use App\Models\Profile;
use App\Support\Logo;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;

class LogbookPdfExporter
{
    public function render(Profile $profil, array $minggu): DomPdf
    {
        return Pdf::loadView('exports.logbook-pdf', [
            'profil' => $profil,
            'minggu' => $minggu,
            'logo'   => Logo::dataUri(),
        ])->setPaper('a4', 'portrait');
    }
}
