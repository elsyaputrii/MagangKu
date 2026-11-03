@extends('layouts.app')
@section('title', 'Tambah Presensi')

@section('content')
<div class="page-head">
    <div>
        <h1>Tambah Presensi</h1>
        <div class="muted">Tanggal otomatis ditempatkan pada kolom hari (Senin–Jumat) dan minggu I–V yang sesuai.</div>
    </div>
</div>
<div class="card">
    @include('presensi._form')
</div>
@endsection
