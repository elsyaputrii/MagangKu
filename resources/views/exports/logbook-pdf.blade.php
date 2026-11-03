@use('App\Support\WeekHelper')
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Logbook Magang Minggu ke {{ $minggu['nomor'] }}</title>
<style>
    @page { margin: 34pt 34pt 40pt 52pt; }
    body { font-family: "DejaVu Serif", "Times New Roman", serif; font-size: 9pt; color: #000; line-height: 1.35; }
    table { border-collapse: collapse; }
    .kop { width: 100%; border-bottom: 3pt double #000; margin-bottom: 14pt; }
    .kop td { vertical-align: middle; padding-bottom: 4pt; }
    .kop .t1 { font-family: "DejaVu Sans", Arial, sans-serif; font-weight: bold; font-size: 11pt; }
    .kop .t2 { font-family: "DejaVu Sans", Arial, sans-serif; font-weight: bold; font-size: 10pt; padding-top: 3pt; }
    .center { text-align: center; }
    .bold { font-weight: bold; }
    table.id { width: 100%; }
    table.id td { vertical-align: top; padding: 0; }
    table.id td.l { width: 126pt; }
    table.id td.c { width: 8pt; }
    table.box { width: 100%; border: 0.75pt solid #000; }
    table.box td { padding: 6pt 8pt 2pt 8pt; text-align: justify; }
    .tgl { font-weight: bold; margin-bottom: 1pt; }
    .uraian { white-space: pre-line; font-size: 8.5pt; margin-bottom: 4pt; }
    .ttd { margin-left: 270pt; margin-top: 18pt; page-break-inside: avoid; }
</style>
</head>
<body>

<table class="kop">
    <tr>
        <td width="62pt">@if ($logo)<img src="{{ $logo }}" style="width:54pt;">@endif</td>
        <td>
            <div class="t1">No.BO.8.4.3.1-V2 Borang Logbook Magang</div>
            <div class="t2">23 Maret 2020</div>
        </td>
    </tr>
</table>

<div class="center bold">Logbook Magang Industri Minggu ke {{ $minggu['nomor'] }}</div>
<div class="center">Tgl {{ WeekHelper::tanggalStrip($minggu['mulai']) }} s/d tgl {{ WeekHelper::tanggalStrip($minggu['selesai']) }}</div>
<br>

<div class="bold">Identitas Mahasiswa</div>
<table class="id">
    <tr><td class="l">Nama Mahasiswa</td><td class="c">:</td><td>{{ $profil->nama }}</td></tr>
    <tr><td class="l">NIM</td><td class="c">:</td><td>{{ $profil->nim }}</td></tr>
    <tr><td class="l">No. Tlp/Hp</td><td class="c">:</td><td>{{ $profil->no_hp }}</td></tr>
</table>
<br>

<div class="bold">Identitas Perusahaan dan Pembimbing di Perusahaan</div>
<table class="id">
    <tr><td class="l">Nama Perusahaan</td><td class="c">:</td><td>{{ $profil->perusahaan_nama }}</td></tr>
    <tr>
        <td class="l">Alamat / No. Telp</td><td class="c">:</td>
        <td>{{ $profil->perusahaan_alamat }}@if ($profil->perusahaan_telp)<br>Phone : {{ $profil->perusahaan_telp }}@endif</td>
    </tr>
    <tr><td class="l">Pembimbing</td><td class="c">:</td><td>{{ $profil->pembimbing_nama }}</td></tr>
    <tr><td class="l">Jabatan</td><td class="c">:</td><td>{{ $profil->pembimbing_jabatan }}</td></tr>
    <tr><td class="l">No. Tlp / Hp</td><td class="c">:</td><td>{{ $profil->pembimbing_hp }}</td></tr>
</table>
<br>

<div class="bold" style="margin-bottom:5pt">Rincian Kegiatan Magang</div>
<table class="box">
    @forelse ($minggu['items'] as $item)
        <tr>
            <td style="page-break-inside: avoid;">
                <div class="tgl">&bull; {{ WeekHelper::tanggalLogbook($item->tanggal) }}</div>
                <div class="uraian">{{ $item->uraian }}</div>
            </td>
        </tr>
    @empty
        <tr><td style="height:80pt">Belum ada kegiatan pada minggu ini.</td></tr>
    @endforelse
</table>

<div class="ttd">
    {{ $profil->kota ?: 'Batam' }}, {{ WeekHelper::tanggalIndo($minggu['selesai']) }}<br>
    Pembimbing di Perusahaan
    <br><br><br><br>
    ({{ $profil->pembimbing_nama }})
</div>

</body>
</html>
