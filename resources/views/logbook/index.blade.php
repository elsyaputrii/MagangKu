@extends('layouts.app')
@section('title', 'Logbook')

@section('content')
@use('App\Support\WeekHelper')
@php
    // Minggu yang terbuka otomatis: minggu yang memuat hari ini, jika tidak ada maka minggu terbaru
    $hariIni = \Carbon\Carbon::today();
    $aktifIndex = $minggu->search(fn ($m) => $hariIni->between($m['mulai'], $m['selesai']->copy()->addDays(2)));
    if ($aktifIndex === false) { $aktifIndex = 0; }
    $aktif = $minggu->get($aktifIndex);
@endphp

<style>
    .wk{border:1px solid var(--line);border-radius:var(--radius);background:#fff;margin-top:12px}
    .wk > summary{list-style:none;cursor:pointer;display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 18px}
    .wk > summary::-webkit-details-marker{display:none}
    .wk > summary:hover{background:#fafcff;border-radius:var(--radius)}
    .wk-title{font-weight:700;font-size:1.02rem}
    .wk-sub{color:var(--muted);font-size:.85rem;margin-top:3px}
    .wk-hint{color:var(--muted);font-size:.8rem;margin-top:2px}
    .wk[open] .wk-hint{display:none}
    .wk .chev{width:20px;height:20px;flex:none;color:var(--muted);transition:transform .2s}
    .wk[open] .chev{transform:rotate(180deg)}
    .wk-body{border-top:1px solid var(--line);margin:0 18px;padding:12px 0 6px}
    .wk-body .log-item:first-of-type{border-top:0}
</style>

<section class="card">
    <div class="card-head">
        <div>
            <h2>Logbook Magang</h2>
            <div class="muted small">Kegiatan dikelompokkan per minggu. Klik minggu untuk membuka atau menutup.</div>
        </div>
        <div class="actions">
            @if ($aktif)<span class="badge ok">Minggu ke-{{ $aktif['nomor'] }}</span>@endif
            <a class="btn btn-primary btn-sm" href="{{ route('logbook.create') }}">@include('partials.icon', ['name' => 'plus']) Tambah kegiatan</a>
        </div>
    </div>

    @forelse ($minggu as $i => $m)
        <details class="wk" @if ($i === $aktifIndex) open @endif>
            <summary>
                <div>
                    <div class="wk-title">Minggu {{ $m['nomor'] }}</div>
                    <div class="wk-sub">
                        {{ WeekHelper::tanggalIndo($m['mulai']) }} – {{ WeekHelper::tanggalIndo($m['selesai']) }}
                        · <span class="badge">{{ $m['items']->count() }} kegiatan</span>
                    </div>
                    <div class="wk-hint">Klik untuk melihat logbook minggu {{ $m['nomor'] }}</div>
                </div>
                <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
            </summary>

            <div class="wk-body">
                <div class="actions" style="margin-bottom:6px">
                    <a class="btn btn-word btn-sm" href="{{ route('logbook.export.word', ['minggu' => $m['mulai']->toDateString()]) }}">@include('partials.icon', ['name' => 'file']) Unduh Word</a>
                    <a class="btn btn-pdf btn-sm" href="{{ route('logbook.export.pdf', ['minggu' => $m['mulai']->toDateString()]) }}">@include('partials.icon', ['name' => 'file']) Unduh PDF</a>
                </div>

                @foreach ($m['items'] as $l)
                    <div class="log-item">
                        <div class="date">
                            <b>{{ WeekHelper::namaHari($l->tanggal) }}</b>
                            <span class="muted">{{ $l->tanggal->format('d / m / Y') }}</span>
                            @if ($l->otomatis)<span class="badge" style="margin-top:6px;width:max-content">Otomatis</span>@endif
                        </div>
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
        </details>
    @empty
        <div class="empty">Belum ada logbook. <a href="{{ route('logbook.create') }}">Tambah kegiatan pertama</a></div>
    @endforelse
</section>
@endsection