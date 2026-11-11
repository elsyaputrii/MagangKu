@extends('layouts.app')
@section('title', 'Logbook')

@section('content')
@use('App\Support\WeekHelper')
@include('partials.week-accordion-style')

<section class="card">
    <div class="card-head">
        <div>
            <h2>Logbook Magang</h2>
            <div class="muted small">Pilih bulan lalu tekan Tampilkan. Klik minggu untuk membuka atau menutup.</div>
        </div>
        <div class="actions">
            <span class="badge ok">Minggu ke-{{ $mingguMagangIni }}</span>
            <a class="btn btn-primary btn-sm" href="{{ route('logbook.create') }}">@include('partials.icon', ['name' => 'plus']) Tambah kegiatan</a>
        </div>
    </div>

    <form method="GET" action="{{ route('logbook.index') }}" class="filters">
        <div class="field">
            <label>Periode</label>
            @include('partials.month-picker', ['name' => 'bulan', 'value' => $bulan])
        </div>
        <button class="btn btn-primary" type="submit">Tampilkan</button>
    </form>

    @forelse ($minggu as $i => $m)
        <details class="acc" @if ($i === 0) open @endif>
            <summary>
                <div>
                    <div class="acc-title">{{ $m['kode'] }}</div>
                    <div class="acc-sub">
                        {{ WeekHelper::rentangIndo($m['mulai'], $m['selesai']) }}
                        · Minggu magang ke-{{ $m['nomor_magang'] }}
                        · <span class="badge">{{ $m['items']->count() }} kegiatan</span>
                    </div>
                    <div class="acc-hint">Klik untuk melihat logbook {{ $m['kode'] }}</div>
                </div>
                <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
            </summary>

            <div class="acc-body">
                <div class="actions" style="margin-bottom:6px">
                    <a class="btn btn-word btn-sm" href="{{ route('logbook.export.word', ['minggu' => $m['senin']->toDateString()]) }}">@include('partials.icon', ['name' => 'file']) Unduh Word</a>
                    <a class="btn btn-pdf btn-sm" href="{{ route('logbook.export.pdf', ['minggu' => $m['senin']->toDateString()]) }}">@include('partials.icon', ['name' => 'file']) Unduh PDF</a>
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
        <div class="empty">Belum Ada Data Logbook pada periode ini</div>
    @endforelse
</section>
@endsection