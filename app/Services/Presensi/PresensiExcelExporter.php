<?php

namespace App\Services\Presensi;

use App\Models\Profile;
use App\Support\WeekHelper;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Mengisi template Form_Presensi_Magang.xlsx (logo, border, ukuran sel dipertahankan).
 * Baris 8-12 = minggu I-V; kolom: Senin B-D, Selasa E-G, Rabu H-J, Kamis K-M, Jumat N-P.
 */
class PresensiExcelExporter
{
    /** @return string path file .xlsx sementara */
    public function generate(Profile $profil, int $tahun, int $bulan, Collection $records): string
    {
        $spreadsheet = IOFactory::load(config('magang.template_presensi_xlsx'));
        $ws = $spreadsheet->getActiveSheet();

        // Identitas
        $this->teks($ws, 'B4', $profil->nama);
        $this->teks($ws, 'B5', $profil->nim);
        $this->teks($ws, 'O4', $profil->perusahaan_nama);
        $this->teks($ws, 'O5', WeekHelper::namaBulan($bulan));

        // Kolom tanggal sedikit dilebarkan supaya "28/9/2026" tidak terpotong.
        foreach (['B', 'E', 'H', 'K', 'N'] as $kolom) {
            $ws->getColumnDimension($kolom)->setWidth(11);
        }

        // Kosongkan contoh data bawaan template (baris 8-12, kolom B-P).
        for ($baris = 8; $baris <= 12; $baris++) {
            for ($kolom = 2; $kolom <= 16; $kolom++) {
                $ws->getCell([$kolom, $baris])->setValue(null);
            }
        }

        // Tempatkan setiap presensi pada minggu (baris) dan hari (kolom) yang sesuai.
        foreach ($records as $presensi) {
            $minggu = WeekHelper::weekOfMonth($presensi->tanggal);
            $hari   = $presensi->tanggal->dayOfWeekIso - 1;
            if ($hari < 0 || $hari > 4 || $minggu < 1 || $minggu > 5) {
                continue; // Sabtu/Minggu tidak dihitung
            }

            $baris = 7 + $minggu;
            $kolom = 2 + 3 * $hari;

            $this->sel($ws, $kolom, $baris, $presensi->tanggal->format('j/n/Y'));

            // Izin/sakit: tulis status pada sel Masuk+Pulang yang digabung
            if (! $presensi->hadir) {
                $this->sel($ws, $kolom + 1, $baris, mb_strtoupper($presensi->status));
                $ws->mergeCells([$kolom + 1, $baris, $kolom + 2, $baris]);
                continue;
            }

            $this->sel($ws, $kolom + 1, $baris, $presensi->jam_datang_template);
            $this->sel($ws, $kolom + 2, $baris, $presensi->jam_pulang_template);
        }

        // Nomor rekening
        $ws->unmergeCells('A14:C14');
        $ws->mergeCells('A14:H14');
        $this->teks($ws, 'A14', 'Nomor Rekening BNI: ' . ($profil->no_rekening_bni ?? ''));
        $ws->getStyle('A14')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Blok tanda tangan (template asli terpotong; dirapikan dan diratakan tengah)
        foreach (['O14', 'Q14', 'P19'] as $koordinat) {
            $ws->getCell($koordinat)->setValue(null);
        }
        $this->teks($ws, 'N14', ($profil->kota ?: 'Batam') . ', ' . WeekHelper::namaBulan($bulan) . ' ' . $tahun);
        $this->teks($ws, 'N15', 'Pembimbing Magang,');
        $this->teks($ws, 'N19', '(' . $profil->pembimbing_nama . ')');
        foreach ([14, 15, 19] as $baris) {
            $ws->mergeCells("N{$baris}:R{$baris}");
            $ws->getStyle("N{$baris}:R{$baris}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
        }

        // Catatan kaki
        $this->teks($ws, 'A20', '*) Masuk dan Pulang diisi dengan waktu dalam satuan WIB.');
        $this->teks($ws, 'A21', '**) Pembimbing Magang merupakan pembimbing di tempat magang/perusahaan.');
        $this->teks($ws, 'A22', '***) Jam kerja yang kurang dari ' . config('magang.min_jam_kerja') . ' jam terkena sanksi tidak dihitung kehadirannya.');
        $ws->getStyle('A20:A22')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Pengaturan cetak: A4 landscape, muat 1 halaman
        $ws->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToPage(true)
            ->setFitToWidth(1)
            ->setFitToHeight(1)
            ->setHorizontalCentered(true);

        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('presensi_', true) . '.xlsx';
        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        return $path;
    }

    private function teks(Worksheet $ws, string $koordinat, string $nilai): void
    {
        $ws->getCell($koordinat)->setValueExplicit($nilai, DataType::TYPE_STRING);
    }

    /** Isi sel (kolom, baris) sebagai teks, rata tengah. Nilai kosong dilewati. */
    private function sel(Worksheet $ws, int $kolom, int $baris, string $nilai): void
    {
        if ($nilai === '') {
            return;
        }

        $sel = $ws->getCell([$kolom, $baris]);
        $sel->setValueExplicit($nilai, DataType::TYPE_STRING);

        $style = $ws->getStyle($sel->getCoordinate());
        $style->getNumberFormat()->setFormatCode('@');
        $style->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
    }
}
