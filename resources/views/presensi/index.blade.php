@extends('layouts.app')
@section('title', 'Presensi')

@section('content')
@use('App\Support\WeekHelper')
@include('partials.status-style')
@include('partials.week-accordion-style')
@php $hariKerja = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']; @endphp

<div class="page-head">
    <div>
        <h1>Presensi Magang</h1>
        <div class="muted">Sabtu &amp; Minggu tidak dihitung</div>
    </div>
    <a class="btn btn-primary" href="{{ route('presensi.create') }}">@include('partials.icon', ['name' => 'plus']) Tambah presensi</a>
</div>

<section class="card">
    <div class="card-head">
        <form method="GET" action="{{ route('presensi.index') }}" class="filters">
            <div class="field">
                <label>Periode</label>
                @include('partials.month-picker', ['name' => 'bulan', 'value' => $bulan])
            </div>
            <button class="btn btn-primary" type="submit">Tampilkan</button>
        </form>
        <div class="actions">
            <a class="btn btn-xlsx" href="{{ route('presensi.export.excel', ['bulan' => $bulan]) }}">@include('partials.icon', ['name' => 'sheet']) Unduh Excel</a>
            <a class="btn btn-pdf" href="{{ route('presensi.export.pdf', ['bulan' => $bulan]) }}">@include('partials.icon', ['name' => 'file']) Unduh PDF</a>
        </div>
    </div>

    @if ($records->isEmpty())
        <div class="empty">Belum Ada Data Presensi pada periode ini</div>
    @endif
</section>

@if ($records->isNotEmpty())
<section class="card">
    <div class="card-head"><h2>Rekap bulanan · {{ $namaBulan }}</h2></div>

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
                        @if ($p && ! $p->hadir)
                            <td class="st-{{ $p->status }}"><a href="{{ route('presensi.edit', $p) }}" title="Ubah">{{ $p->tanggal->format('d/m/y') }}</a></td>
                            <td colspan="2" class="st-{{ $p->status }}" title="{{ $p->keterangan }}">{{ strtoupper($p->status) }}</td>
                        @elseif ($p)
                            <td class="{{ $p->kurang_jam ? 'warn-cell' : '' }}"><a href="{{ route('presensi.edit', $p) }}" title="Ubah">{{ $p->tanggal->format('d/m/y') }}</a></td>
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

    @foreach ($mingguList as $i => $m)
        <details class="acc" @if ($i === 0) open @endif>
            <summary>
                <div>
                    <div class="acc-title">{{ $m['kode'] }}</div>
                    <div class="acc-sub">
                        {{ WeekHelper::rentangIndo($m['mulai'], $m['selesai']) }}
                        · <span class="badge">{{ $m['items']->count() }} hari</span>
                    </div>
                    <div class="acc-hint">Klik untuk melihat presensi {{ $m['kode'] }}</div>
                </div>
                <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
            </summary>

            <div class="acc-body">
                <div class="table-wrap">
                    <table class="data">
                        <thead>
                            <tr><th>Tanggal</th><th>Datang</th><th>Pulang</th><th>Durasi</th><th>Status</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                        @foreach ($m['items'] as $p)
                            <tr>
                                <td class="nowrap">{{ WeekHelper::namaHari($p->tanggal) }}, {{ $p->tanggal->format('d/m/y') }}</td>
                                <td>{{ $p->hadir ? $p->jam_datang_template : '-' }}</td>
                                <td>{{ $p->jam_pulang_template ?: '-' }}</td>
                                <td class="nowrap">{{ $p->durasi_teks }}</td>
                                <td>
                                    @if (! $p->hadir)
                                        <span class="badge {{ $p->status }}">{{ $p->status_label }}</span>
                                        @if ($p->keterangan)<div class="muted small">{{ $p->keterangan }}</div>@endif
                                    @elseif ($p->jam_pulang === null)
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
                                        <form method="POST" action="{{ route('presensi.destroy', $p) }}" data-confirm="Hapus presensi tanggal {{ $p->tanggal->format('d/m/y') }}?">
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
            </div>
        </details>
    @endforeach
</section>
@endif
@endsection