<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use App\Services\Logbook\LogbookPdfExporter;
use App\Services\Logbook\LogbookService;
use App\Services\Logbook\LogbookWordExporter;
use App\Services\Profil\ProfilService;
use App\Support\WeekHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExportLogbookController extends Controller
{
    public function __construct(
        private readonly LogbookService $logbook,
        private readonly ProfilService $profil,
        private readonly LogbookWordExporter $word,
        private readonly LogbookPdfExporter $pdf
    ) {
    }

    public function word(Request $request)
    {
        [$profil, $minggu] = $this->siapkan($request);

        $path = $this->word->generate($profil, $minggu);

        return response()
            ->download($path, $this->namaFile($minggu, 'docx'))
            ->deleteFileAfterSend(true);
    }

    public function pdf(Request $request)
    {
        [$profil, $minggu] = $this->siapkan($request);

        return $this->pdf->render($profil, $minggu)->download($this->namaFile($minggu, 'pdf'));
    }

    /** @return array{0: \App\Models\Profile, 1: array} */
    private function siapkan(Request $request): array
    {
        $request->validate(['minggu' => ['required', 'date_format:Y-m-d']]);

        $senin = WeekHelper::weekStart(Carbon::parse($request->query('minggu')));

        return [$this->profil->get(), $this->logbook->week($this->profil->get(), $senin)];
    }

    private function namaFile(array $minggu, string $ext): string
    {
        return sprintf('Logbook_Magang_Minggu_%d_%s.%s', $minggu['nomor'], $minggu['mulai']->format('Y-m-d'), $ext);
    }
}
