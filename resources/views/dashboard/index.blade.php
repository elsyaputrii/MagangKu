@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
@use('App\Support\WeekHelper')

<div class="page-head">
    <div>
        <h1>Halo, {{ $profil->nama }}</h1>
        <div class="muted">{{ $profil->perusahaan_nama }} · Minggu ini: {{ $ringkasan['minggu_ini'] }}</div>
    </div>
    <div class="actions">
        <a class="btn btn-primary" href="{{ route('presensi.create') }}">@include('partials.icon', ['name' => 'plus']) Presensi</a>
        <a class="btn btn-primary" href="{{ route('logbook.create') }}">@include('partials.icon', ['name' => 'plus']) Logbook</a>
    </div>
</div>

@if ($profil->nim === '-' || $profil->nama === 'Nama Mahasiswa')
    <div class="alert alert-info">Profil belum diisi. <a href="{{ route('profil.edit') }}">Lengkapi profil</a> agar hasil export sesuai identitas Anda.</div>
@endif

<div class="grid stats">
    <div class="card stat ok">
        <div class="ico">@include('partials.icon', ['name' => 'check'])</div>
        <div><div class="num">{{ $ringkasan['presensi_total'] }}</div><div class="lbl">Total hari presensi</div></div>
    </div>
    <div class="card stat">
        <div class="ico">@include('partials.icon', ['name' => 'calendar'])</div>
        <div><div class="num">{{ $ringkasan['presensi_bulan_ini'] }}</div><div class="lbl">Presensi {{ $ringkasan['bulan_ini'] }}</div></div>
    </div>
    <div class="card stat ok">
        <div class="ico">@include('partials.icon', ['name' => 'book'])</div>
        <div><div class="num">{{ $ringkasan['logbook_total'] }}</div><div class="lbl">Total kegiatan logbook</div></div>
    </div>
    <div class="card stat">
        <div class="ico">@include('partials.icon', ['name' => 'file'])</div>
        <div><div class="num">{{ $ringkasan['logbook_minggu_ini'] }}</div><div class="lbl">Logbook minggu ini ({{ $ringkasan['logbook_minggu_total'] }} minggu tercatat)</div></div>
    </div>
    <div class="card stat warn">
        <div class="ico">@include('partials.icon', ['name' => 'alert'])</div>
        <div>
            <div class="num">{{ $ringkasan['presensi_kurang_jam'] }}</div>
            <div class="lbl">Hari &lt; {{ config('magang.min_jam_kerja') }} jam kerja</div>
        </div>
    </div>
    <div class="card stat warn">
        <div class="ico">@include('partials.icon', ['name' => 'clock'])</div>
        <div><div class="num">{{ $ringkasan['presensi_belum_pulang'] }}</div><div class="lbl">Hari tanpa jam pulang</div></div>
    </div>
</div>

<div class="grid two">
    <section class="card">
        <div class="card-head">
            <h2>Presensi terbaru</h2>
            <a class="btn btn-sm" href="{{ route('presensi.index') }}">Lihat semua</a>
        </div>
        @if ($ringkasan['presensi_terbaru']->isEmpty())
            <div class="empty">Belum ada presensi. <a href="{{ route('presensi.create') }}">Tambah presensi</a></div>
        @else
            <div class="table-wrap">
                <table class="data">
                    <thead><tr><th>Tanggal</th><th>Datang</th><th>Pulang</th><th>Durasi</th></tr></thead>
                    <tbody>
                    @foreach ($ringkasan['presensi_terbaru'] as $p)
                        <tr>
                            <td>{{ WeekHelper::namaHari($p->tanggal) }}, {{ $p->tanggal->format('d/m/Y') }}</td>
                            <td>{{ $p->jam_datang_template }}</td>
                            <td>{{ $p->jam_pulang_template ?: '-' }}</td>
                            <td>
                                {{ $p->durasi_teks }}
                                @if ($p->kurang_jam) <span class="badge warn">&lt; {{ config('magang.min_jam_kerja') }} jam</span> @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <section class="card">
        <div class="card-head">
            <h2>Kegiatan logbook terbaru</h2>
            <a class="btn btn-sm" href="{{ route('logbook.index') }}">Lihat semua</a>
        </div>
        @forelse ($ringkasan['logbook_terbaru'] as $l)
            <div style="padding:10px 0;border-top:1px solid var(--line)">
                <b>{{ WeekHelper::namaHari($l->tanggal) }}, {{ $l->tanggal->format('d/m/Y') }}</b>
                <div class="muted small">{{ \Illuminate\Support\Str::limit($l->uraian, 140) }}</div>
            </div>
        @empty
            <div class="empty">Belum ada kegiatan. <a href="{{ route('logbook.create') }}">Tambah logbook</a></div>
        @endforelse
    </section>
</div>
@endsection
