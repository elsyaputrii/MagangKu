<?php

namespace App\Services\Logbook;

use App\Models\Profile;
use App\Support\WeekHelper;
use RuntimeException;
use ZipArchive;

/**
 * Membuat logbook mingguan dari file template .docx asli:
 * header (logo, nomor borang, garis ganda), style, dan ukuran halaman dipertahankan;
 * hanya isi <w:body> yang diganti dengan data minggu terpilih.
 */
class LogbookWordExporter
{
    private const FONT = '<w:rFonts w:ascii="Cambria" w:hAnsi="Cambria" w:cs="Arial"/>';
    private const TAB_LABEL = 2835;   // posisi titik dua pada identitas
    private const LEBAR_TABEL = 9639; // lebar area tulis (A4, margin 2 cm)
    private const INDENT_TTD = 5600;

    /** @return string path file .docx sementara */
    public function generate(Profile $profil, array $minggu): string
    {
        $template = config('magang.template_logbook_docx');
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('logbook_', true) . '.docx';

        if (! copy($template, $path)) {
            throw new RuntimeException('Template logbook tidak dapat disalin.');
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new RuntimeException('Template logbook tidak dapat dibuka.');
        }

        $xml = $zip->getFromName('word/document.xml');
        if ($xml === false) {
            $zip->close();
            throw new RuntimeException('Template logbook tidak valid (document.xml tidak ditemukan).');
        }

        $awalBody = strpos($xml, '<w:body>');
        preg_match_all('#<w:sectPr.*?</w:sectPr>#s', $xml, $cocok);
        $sectPr = end($cocok[0]) ?: '';

        $dokumen = substr($xml, 0, $awalBody + strlen('<w:body>'))
            . $this->body($profil, $minggu)
            . $sectPr
            . '</w:body></w:document>';

        $zip->addFromString('word/document.xml', $dokumen);
        $zip->close();

        return $path;
    }

    private function body(Profile $profil, array $minggu): string
    {
        $mulai   = $minggu['mulai'];
        $selesai = $minggu['selesai'];

        $b  = $this->para($this->run('Logbook Magang Industri Minggu ke ' . $minggu['nomor'], 20, true), 'center');
        $b .= $this->para($this->run('Tgl ' . WeekHelper::tanggalStrip($mulai) . ' s/d tgl ' . WeekHelper::tanggalStrip($selesai)), 'center');
        $b .= $this->para();

        $b .= $this->para($this->run('Identitas Mahasiswa', 20, true), null, null, null, null, true);
        $b .= $this->barisIdentitas('Nama Mahasiswa', $profil->nama);
        $b .= $this->barisIdentitas('NIM', $profil->nim);
        $b .= $this->barisIdentitas('No. Tlp/Hp', (string) $profil->no_hp);
        $b .= $this->para();

        $alamat = (string) $profil->perusahaan_alamat;
        if ($profil->perusahaan_telp) {
            $alamat .= "\nPhone : " . $profil->perusahaan_telp;
        }

        $b .= $this->para($this->run('Identitas Perusahaan dan Pembimbing di Perusahaan', 20, true), null, null, null, null, true);
        $b .= $this->barisIdentitas('Nama Perusahaan', $profil->perusahaan_nama);
        $b .= $this->barisIdentitas('Alamat / No. Telp', $alamat);
        $b .= $this->barisIdentitas('Pembimbing', $profil->pembimbing_nama);
        $b .= $this->barisIdentitas('Jabatan', (string) $profil->pembimbing_jabatan);
        $b .= $this->barisIdentitas('No. Tlp / Hp', (string) $profil->pembimbing_hp);
        $b .= $this->para();

        $b .= $this->para($this->run('Rincian Kegiatan Magang', 20, true), null, null, null, null, true);
        $b .= $this->para('', null, null, 'w:before="0" w:after="0" w:line="120" w:lineRule="exact"', null, false, 10);
        $b .= $this->tabelKegiatan($minggu['items']);
        $b .= $this->para();

        // Tanda tangan: tanggal mengikuti hari Jumat pada minggu tersebut (sesuai contoh template)
        $indent = 'w:left="' . self::INDENT_TTD . '"';
        $b .= $this->para(
            $this->run(($profil->kota ?: 'Batam') . ', ' . WeekHelper::tanggalIndo($selesai)),
            null, $indent, null, null, true
        );
        $b .= $this->para($this->run('Pembimbing di Perusahaan'), null, $indent, null, null, true);
        for ($i = 0; $i < 4; $i++) {
            $b .= $this->para('', null, null, null, null, true);
        }
        $b .= $this->para($this->run('(' . $profil->pembimbing_nama . ')'), null, $indent);

        return $b;
    }

    /** Satu baris per kegiatan; garis antar-baris dihilangkan agar tampak sebagai satu kotak. */
    private function tabelKegiatan($items): string
    {
        $w = self::LEBAR_TABEL;
        $border = '';
        foreach (['top', 'left', 'bottom', 'right'] as $sisi) {
            $border .= '<w:' . $sisi . ' w:val="single" w:sz="4" w:space="0" w:color="auto"/>';
        }

        $baris = '';
        foreach ($items as $item) {
            $isi  = $this->para(
                $this->run("\u{2022} ", 18) . $this->run(WeekHelper::tanggalLogbook($item->tanggal), 18, true),
                'left', null, 'w:before="160" w:after="20"', null, false, 18
            );
            $isi .= $this->para(
                $this->multiline((string) $item->uraian, 18),
                'both', null, 'w:before="0" w:after="100"', null, false, 18
            );
            $baris .= $this->baris($isi, $w);
        }

        if ($baris === '') {
            $baris = $this->baris(
                $this->para(
                    $this->run('Belum ada kegiatan pada minggu ini.', 18),
                    null, null, 'w:before="160" w:after="100"', null, false, 18
                ),
                $w,
                1200
            );
        }

        return '<w:tbl><w:tblPr><w:tblW w:w="' . $w . '" w:type="dxa"/>'
            . '<w:tblBorders>' . $border . '</w:tblBorders><w:tblLayout w:type="fixed"/>'
            . '<w:tblCellMar><w:left w:w="140" w:type="dxa"/><w:right w:w="140" w:type="dxa"/></w:tblCellMar></w:tblPr>'
            . '<w:tblGrid><w:gridCol w:w="' . $w . '"/></w:tblGrid>' . $baris . '</w:tbl>';
    }

    private function baris(string $isi, int $lebar, ?int $tinggiMin = null): string
    {
        $trPr = '<w:trPr><w:cantSplit/>'
            . ($tinggiMin ? '<w:trHeight w:val="' . $tinggiMin . '" w:hRule="atLeast"/>' : '')
            . '</w:trPr>';

        return '<w:tr>' . $trPr . '<w:tc><w:tcPr><w:tcW w:w="' . $lebar . '" w:type="dxa"/></w:tcPr>' . $isi . '</w:tc></w:tr>';
    }

    private function barisIdentitas(string $label, string $nilai): string
    {
        $tab = self::TAB_LABEL;

        return $this->para(
            $this->run($label) . $this->tab() . $this->multiline(': ' . $nilai),
            'left',
            'w:left="' . $tab . '" w:hanging="' . $tab . '"',
            null,
            [$tab]
        );
    }

    // ---------- Helper WordprocessingML ----------

    private function rpr(int $sz = 20, bool $bold = false): string
    {
        return '<w:rPr>' . self::FONT . ($bold ? '<w:b/><w:bCs/>' : '')
            . '<w:sz w:val="' . $sz . '"/><w:szCs w:val="' . $sz . '"/></w:rPr>';
    }

    private function run(string $teks, int $sz = 20, bool $bold = false): string
    {
        return '<w:r>' . $this->rpr($sz, $bold) . '<w:t xml:space="preserve">' . $this->esc($teks) . '</w:t></w:r>';
    }

    private function tab(int $sz = 20): string
    {
        return '<w:r>' . $this->rpr($sz) . '<w:tab/></w:r>';
    }

    private function br(int $sz = 20): string
    {
        return '<w:r>' . $this->rpr($sz) . '<w:br/></w:r>';
    }

    /** Teks dengan baris baru -> <w:br/>. */
    private function multiline(string $teks, int $sz = 20): string
    {
        $bagian = preg_split('/\r\n|\r|\n/', $teks);
        $hasil = '';
        foreach ($bagian as $i => $baris) {
            $hasil .= ($i > 0 ? $this->br($sz) : '') . $this->run($baris, $sz);
        }

        return $hasil;
    }

    private function para(
        string $isi = '',
        ?string $jc = null,
        ?string $indent = null,
        ?string $spacing = null,
        ?array $tabs = null,
        bool $keepNext = false,
        int $sz = 20
    ): string {
        $ppr = '';
        if ($keepNext) {
            $ppr .= '<w:keepNext/>';
        }
        if ($tabs) {
            $ppr .= '<w:tabs>';
            foreach ($tabs as $pos) {
                $ppr .= '<w:tab w:val="left" w:pos="' . $pos . '"/>';
            }
            $ppr .= '</w:tabs>';
        }
        if ($spacing) {
            $ppr .= '<w:spacing ' . $spacing . '/>';
        }
        if ($indent) {
            $ppr .= '<w:ind ' . $indent . '/>';
        }
        if ($jc) {
            $ppr .= '<w:jc w:val="' . $jc . '"/>';
        }
        $ppr .= $this->rpr($sz);

        return '<w:p><w:pPr>' . $ppr . '</w:pPr>' . $isi . '</w:p>';
    }

    private function esc(string $teks): string
    {
        // buang karakter kontrol yang tidak valid di XML, lalu escape
        $teks = preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '', $teks) ?? '';

        return htmlspecialchars($teks, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
