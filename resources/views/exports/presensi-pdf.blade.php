<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Presensi Magang {{ $namaBulan }} {{ $tahun }}</title>
<style>
    @page { margin: 26pt 30pt 24pt 30pt; }
    body { font-family: "DejaVu Sans", Helvetica, Arial, sans-serif; font-size: 8.5pt; color: #000; }
    table { border-collapse: collapse; }
    .head td { vertical-align: middle; }
    .title { text-align: center; font-size: 17pt; font-weight: bold; padding-bottom: 4pt; }
    .info td { padding: 3pt 0; font-weight: bold; font-size: 9pt; }
    table.grid { width: 100%; table-layout: fixed; margin-top: 6pt; }
    table.grid th, table.grid td { border: 0.75pt solid #000; text-align: center; vertical-align: middle; padding: 2pt 1pt; }
    table.grid th { font-weight: bold; font-size: 8.5pt; }
    table.grid td { height: 29pt; font-size: 8.5pt; }
    table.grid td.wk { font-weight: normal; }
    .foot { width: 100%; margin-top: 8pt; }
    .foot td { vertical-align: top; }
    .ttd { text-align: center; }
    .notes { font-size: 8pt; margin-top: 6pt; line-height: 1.5; }
</style>
</head>
<body>

<table class="head" width="100%">
    <tr>
        <td width="20%">@if ($logo)<img src="{{ $logo }}" style="width:78pt;">@endif</td>
        <td width="60%" class="title">PRESENSI MAGANG/ PKL</td>
        <td width="20%"></td>
    </tr>
</table>

<table class="info" width="100%" style="margin-top:6pt">
    <tr>
        <td width="12%">Nama</td>
        <td width="43%">{{ $profil->nama }}</td>
        <td width="17%">Nama Perusahaan</td>
        <td width="28%">{{ $profil->perusahaan_nama }}</td>
    </tr>
    <tr>
        <td>NIM/ NIS</td>
        <td>{{ $profil->nim }}</td>
        <td>Bulan</td>
        <td>{{ $namaBulan }} {{ $tahun }}</td>
    </tr>
</table>

@php $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']; @endphp
<table class="grid">
    <colgroup>
        <col style="width:6%">
        @foreach ($hari as $h)
            <col style="width:7.4%"><col style="width:5.6%"><col style="width:5.6%">
        @endforeach
        <col style="width:5.5%"><col style="width:5.5%">
    </colgroup>
    <thead>
        <tr>
            <th rowspan="2">Minggu<br>Ke</th>
            @foreach ($hari as $h)
                <th colspan="3">{{ $h }}</th>
            @endforeach
            <th colspan="2" rowspan="2">Paraf<br>Pembimbing</th>
        </tr>
        <tr>
            @foreach ($hari as $h)
                <th>Tgl</th><th>Masuk</th><th>Pulang</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
    @foreach ($grid as $minggu => $hariHari)
        <tr>
            <td class="wk">{{ $romawi[$minggu] }}</td>
            @foreach ($hariHari as $p)
                @if ($p && ! $p->hadir)
                    <td>{{ $p->tanggal->format('j/n/Y') }}</td>
                    <td colspan="2" style="font-weight:bold;">{{ strtoupper($p->status) }}</td>
                @elseif ($p)
                    <td>{{ $p->tanggal->format('j/n/Y') }}</td>
                    <td>{{ $p->jam_datang_template }}</td>
                    <td>{{ $p->jam_pulang_template }}</td>
                @else
                    <td></td><td></td><td></td>
                @endif
            @endforeach
            <td></td><td></td>
        </tr>
    @endforeach
    </tbody>
</table>

<table class="foot">
    <tr>
        <td width="60%">Nomor Rekening BNI: {{ $profil->no_rekening_bni }}</td>
        <td width="40%" class="ttd">
            {{ $profil->kota ?: 'Batam' }}, {{ $namaBulan }} {{ $tahun }}<br>
            Pembimbing Magang,<br><br><br><br>
            ({{ $profil->pembimbing_nama }})
        </td>
    </tr>
</table>

<div class="notes">
    *) Masuk dan Pulang diisi dengan waktu dalam satuan WIB.<br>
    **) Pembimbing Magang merupakan pembimbing di tempat magang/perusahaan.<br>
    ***) Jam kerja yang kurang dari {{ $minJam }} jam terkena sanksi tidak dihitung kehadirannya.
</div>

</body>
</html>
