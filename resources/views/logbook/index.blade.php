@extends('layouts.app')
@section('title', 'Logbook')

@section('content')
@use('App\Support\WeekHelper')

<div class="page-head">
    <div>
        <h1>Logbook Magang</h1>
        <div class="muted">Kegiatan dikelompokkan per minggu. Unduh rekap mingguan dalam format Word atau PDF.</div>
    </div>
    <a class="btn btn-primary" href="{{ route('logbook.create') }}">@include('partials.icon', ['name' => 'plus']) Tambah kegiatan</a>
</div>

@forelse ($minggu as $m)
    <section class="card">
        <div class="week-head">
            <div>
                <h2>Minggu ke-{{ $m['nomor'] }}</h2>
                <div class="muted small">
                    {{ WeekHelper::tanggalIndo($m['mulai']) }} – {{ WeekHelper::tanggalIndo($m['selesai']) }}
                    · <span class="badge">{{ $m['items']->count() }} kegiatan</span>
                </div>
            </div>
            <div class="actions">
                <a class="btn btn-word" href="{{ route('logbook.export.word', ['minggu' => $m['mulai']->toDateString()]) }}">@include('partials.icon', ['name' => 'file']) Unduh Word</a>
                <a class="btn btn-pdf" href="{{ route('logbook.export.pdf', ['minggu' => $m['mulai']->toDateString()]) }}">@include('partials.icon', ['name' => 'file']) Unduh PDF</a>
            </div>
        </div>

        <div style="margin-top:12px">
            @foreach ($m['items'] as $l)
                <div class="log-item">
                    <div class="date"><b>{{ WeekHelper::namaHari($l->tanggal) }}</b><span class="muted">{{ $l->tanggal->format('d / m / Y') }}</span></div>
                    <div class="text">{{ $l->uraian }}</div>
                    <div class="actions" style="align-items:flex-start">
                        <a class="btn btn-sm" href="{{ route('logbook.edit', $l) }}">@include('partials.icon', ['name' => 'edit']) Ubah</a>
                        <form method="POST" action="{{ route('logbook.destroy', $l) }}" data-confirm="Hapus logbook tanggal {{ $l->tanggal->format('d/m/Y') }}?">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" type="submit">@include('partials.icon', ['name' => 'trash']) Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@empty
    <div class="card"><div class="empty">Belum ada logbook. <a href="{{ route('logbook.create') }}">Tambah kegiatan pertama</a></div></div>
@endforelse
@endsection
