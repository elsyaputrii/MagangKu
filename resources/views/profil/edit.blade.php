@extends('layouts.app')
@section('title', 'Profil')

@section('content')
@php
    $v = fn ($k, $d = '') => old($k, $profil->{$k} ?? $d);
    $tgl = fn ($k) => old($k, $profil->{$k} ? $profil->{$k}->toDateString() : '');
@endphp

<div class="page-head">
    <div>
        <h1>Profil</h1>
        <div class="muted">Data ini dipakai pada hasil export Excel, Word, dan PDF.</div>
    </div>
</div>

<form method="POST" action="{{ route('profil.update') }}" class="form wide" novalidate>
    @csrf @method('PUT')

    <section class="card">
        <div class="card-head"><h2>Identitas mahasiswa</h2></div>
        <div class="fields">
            <div class="field @error('nama') has-error @enderror">
                <label for="nama">Nama <span class="req">*</span></label>
                <input id="nama" name="nama" value="{{ $v('nama') }}" required>
                @error('nama')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field @error('nim') has-error @enderror">
                <label for="nim">NIM / NIS <span class="req">*</span></label>
                <input id="nim" name="nim" value="{{ $v('nim') }}" required>
                @error('nim')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field @error('no_hp') has-error @enderror">
                <label for="no_hp">No. Tlp / HP</label>
                <input id="no_hp" name="no_hp" value="{{ $v('no_hp') }}" inputmode="tel">
                @error('no_hp')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field @error('no_rekening_bni') has-error @enderror">
                <label for="no_rekening_bni">Nomor rekening BNI</label>
                <input id="no_rekening_bni" name="no_rekening_bni" value="{{ $v('no_rekening_bni') }}" inputmode="numeric">
                @error('no_rekening_bni')<div class="err">{{ $message }}</div>@enderror
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-head"><h2>Perusahaan tempat magang</h2></div>
        <div class="fields">
            <div class="field @error('perusahaan_nama') has-error @enderror">
                <label for="perusahaan_nama">Nama perusahaan <span class="req">*</span></label>
                <input id="perusahaan_nama" name="perusahaan_nama" value="{{ $v('perusahaan_nama') }}" required>
                @error('perusahaan_nama')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field @error('perusahaan_telp') has-error @enderror">
                <label for="perusahaan_telp">Telepon perusahaan</label>
                <input id="perusahaan_telp" name="perusahaan_telp" value="{{ $v('perusahaan_telp') }}" inputmode="tel">
                @error('perusahaan_telp')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field @error('kota') has-error @enderror">
                <label for="kota">Kota (untuk tanda tangan) <span class="req">*</span></label>
                <input id="kota" name="kota" value="{{ $v('kota', 'Batam') }}" required>
                @error('kota')<div class="err">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="field @error('perusahaan_alamat') has-error @enderror" style="margin-top:16px">
            <label for="perusahaan_alamat">Alamat perusahaan</label>
            <textarea id="perusahaan_alamat" name="perusahaan_alamat" rows="3" style="min-height:80px">{{ $v('perusahaan_alamat') }}</textarea>
            @error('perusahaan_alamat')<div class="err">{{ $message }}</div>@enderror
        </div>
    </section>

    <section class="card">
        <div class="card-head"><h2>Pembimbing magang (di perusahaan)</h2></div>
        <div class="fields">
            <div class="field @error('pembimbing_nama') has-error @enderror">
                <label for="pembimbing_nama">Nama pembimbing <span class="req">*</span></label>
                <input id="pembimbing_nama" name="pembimbing_nama" value="{{ $v('pembimbing_nama') }}" required>
                @error('pembimbing_nama')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field @error('pembimbing_jabatan') has-error @enderror">
                <label for="pembimbing_jabatan">Jabatan</label>
                <input id="pembimbing_jabatan" name="pembimbing_jabatan" value="{{ $v('pembimbing_jabatan') }}">
                @error('pembimbing_jabatan')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field @error('pembimbing_hp') has-error @enderror">
                <label for="pembimbing_hp">No. Tlp / HP pembimbing</label>
                <input id="pembimbing_hp" name="pembimbing_hp" value="{{ $v('pembimbing_hp') }}" inputmode="tel">
                @error('pembimbing_hp')<div class="err">{{ $message }}</div>@enderror
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-head"><h2>Periode magang</h2></div>
        <div class="fields">
            <div class="field @error('tanggal_mulai') has-error @enderror">
                <label for="tanggal_mulai">Tanggal mulai <span class="req">*</span></label>
                <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ $tgl('tanggal_mulai') }}" required>
                <div class="hint">Dipakai menghitung "Minggu ke-N" pada logbook.</div>
                @error('tanggal_mulai')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field @error('tanggal_selesai') has-error @enderror">
                <label for="tanggal_selesai">Tanggal selesai</label>
                <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ $tgl('tanggal_selesai') }}">
                @error('tanggal_selesai')<div class="err">{{ $message }}</div>@enderror
            </div>
        </div>
    </section>

    <div class="actions">
        <button class="btn btn-primary" type="submit">@include('partials.icon', ['name' => 'check']) Simpan profil</button>
    </div>
</form>
@endsection
