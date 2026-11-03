@extends('layouts.app')
@section('title', 'Presensi')

@section('content')
@use('App\Support\WeekHelper')
@php $hariKerja = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']; @endphp

<div class="page-head">
    <div>
        <h1>Presensi Magang</h1>
        <div class="muted">Rekap bulan {{ $namaBulan }} · Sabtu &amp; Minggu tidak dihitung</div>
    </div>
    <a class="btn btn-primary" href="{{ route('presensi.create') }}">@include('partials.icon', ['name' => 'plus']) Tambah presensi</a>
</div>

<section class="card">
    <div class="card-head">
        <form method="GET" action="{{ route('presensi.index') }}" class="filters">
            <div class="field">
                <label for="bulan">Bulan</label>
                <input type="month" id="bulan" name="bulan" value="{{ $bulan }}">
            </div>
            <button class="btn" type="submit">Tampilkan</button>
        </form>
        <div class="actions">
            <a class="btn btn-xlsx" href="{{ route('presensi.export.excel', ['bulan' => $bulan]) }}">@include('partials.icon', ['name' => 'sheet']) Unduh Excel</a>
            <a class="btn btn-pdf" href="{{ route('presensi.export.pdf', ['bulan' => $bulan]) }}">@include('partials.icon', ['name' => 'file']) Unduh PDF</a>
        </div>
    </div>

    <div class="table-wrap">
        <table class="rekap">
            <thead>
                <tr>
                    <th rowspan="2">Minggu<br>Ke</th>
                    @foreach ($hariKerja as $hari)
                        <th colspan="3">{{ $hari }}</th>
                    @endforeach
                </tr>
                <tr>
                    @foreach ($hariKerja as $hari)
                        <th>Tgl</th><th>Masuk</th><th>Pulang</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
            @foreach ($grid as $minggu => $hariHari)
                <tr>
                    <td class="wk">{{ $romawi[$minggu] }}</td>
                    @foreach ($hariHari as $p)
                        @if ($p)
                            <td class="{{ $p->kurang_jam ? 'warn-cell' : '' }}"><a href="{{ route('presensi.edit', $p) }}" title="Ubah">{{ $p->tanggal->format('j/n/Y') }}</a></td>
                            <td class="{{ $p->kurang_jam ? 'warn-cell' : '' }}">{{ $p->jam_datang_template }}</td>
                            <td class="{{ $p->kurang_jam ? 'warn-cell' : '' }}">{{ $p->jam_pulang_template }}</td>
                        @else
                            <td class="cell-empty"></td><td class="cell-empty"></td><td class="cell-empty"></td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="hint">Sel berwarna kuning = jam kerja kurang dari {{ config('magang.min_jam_kerja') }} jam (tidak dihitung kehadirannya).</div>
</section>

<section class="card">
    <div class="card-head"><h2>Daftar presensi · {{ $namaBulan }}</h2><span class="badge">{{ $records->count() }} hari</span></div>

    @if ($records->isEmpty())
        <div class="empty">Belum ada presensi pada bulan ini.</div>
    @else
        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr><th>Tanggal</th><th>Minggu</th><th>Datang</th><th>Pulang</th><th>Durasi</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                @foreach ($records as $p)
                    <tr>
                        <td class="nowrap">{{ WeekHelper::namaHari($p->tanggal) }}, {{ $p->tanggal->format('d/m/Y') }}</td>
                        <td>{{ $romawi[WeekHelper::weekOfMonth($p->tanggal)] ?? '-' }}</td>
                        <td>{{ $p->jam_datang_template }}</td>
                        <td>{{ $p->jam_pulang_template ?: '-' }}</td>
                        <td class="nowrap">{{ $p->durasi_teks }}</td>
                        <td>
                            @if ($p->jam_pulang === null)
                                <span class="badge warn">Belum pulang</span>
                            @elseif ($p->kurang_jam)
                                <span class="badge warn">&lt; {{ config('magang.min_jam_kerja') }} jam</span>
                            @else
                                <span class="badge ok">Lengkap</span>
                            @endif
                        </td>
                        <td class="nowrap">
                            <div class="actions">
                                <a class="btn btn-sm" href="{{ route('presensi.edit', $p) }}">@include('partials.icon', ['name' => 'edit']) Ubah</a>
                                <form method="POST" action="{{ route('presensi.destroy', $p) }}" data-confirm="Hapus presensi tanggal {{ $p->tanggal->format('d/m/Y') }}?">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" type="submit">@include('partials.icon', ['name' => 'trash']) Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>
@endsection
