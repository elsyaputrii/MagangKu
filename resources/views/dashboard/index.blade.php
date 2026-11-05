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

@php $f = $ringkasan['filter']; @endphp

<form method="GET" action="{{ route('dashboard') }}" class="card filters" style="margin-bottom:16px">
    <div class="field">
        <label for="bulan">Bulan</label>
        <input type="month" id="bulan" name="bulan" value="{{ $f['bulan'] }}"
               onchange="var m=this.form.querySelector('[name=minggu]'); if(m){m.disabled=true;} this.form.submit();">
    </div>
    <div class="field">
        <label for="minggu">Minggu</label>
        <select id="minggu" name="minggu" onchange="this.form.submit()">
            @foreach ($f['pilihan'] as $n => $label)
                <option value="{{ $n }}" @selected($n === $f['minggu'])>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="muted small" style="padding-bottom:10px">
        Menampilkan <b>M{{ $f['minggu'] }} {{ $f['nama_bulan'] }}</b> ({{ $f['rentang'] }})
        @if ($f['minggu_ini']) <span class="badge ok">Minggu ini</span> @endif
    </div>
</form>

<div class="grid two">
    <section class="card">
        <div class="card-head">
            <h2>Presensi · M{{ $f['minggu'] }} {{ $f['nama_bulan'] }}</h2>
            <a class="btn btn-sm" href="{{ route('presensi.index', ['bulan' => $f['bulan']]) }}">Lihat semua</a>
        </div>
        @if ($ringkasan['presensi_minggu']->isEmpty())
            <div class="empty">
                Belum ada presensi pada minggu ini.
                @if ($f['minggu_ini']) <a href="{{ route('presensi.create') }}">Tambah presensi</a> @endif
            </div>
        @else
            <div class="table-wrap">
                <table class="data">
                    <thead><tr><th>Tanggal</th><th>Datang</th><th>Pulang</th><th>Durasi</th></tr></thead>
                    <tbody>
                    @foreach ($ringkasan['presensi_minggu'] as $p)
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
            <h2>Logbook · M{{ $f['minggu'] }} {{ $f['nama_bulan'] }}</h2>
            <a class="btn btn-sm" href="{{ route('logbook.index') }}">Lihat semua</a>
        </div>
        @forelse ($ringkasan['logbook_minggu'] as $l)
            <div style="padding:10px 0;border-top:1px solid var(--line)">
                <b>{{ WeekHelper::namaHari($l->tanggal) }}, {{ $l->tanggal->format('d/m/Y') }}</b>
                <div class="muted small">{{ \Illuminate\Support\Str::limit($l->uraian, 140) }}</div>
            </div>
        @empty
            <div class="empty">
                Belum ada kegiatan pada minggu ini.
                @if ($f['minggu_ini']) <a href="{{ route('logbook.create') }}">Tambah logbook</a> @endif
            </div>
        @endforelse
    </section>
</div>
@endsection
