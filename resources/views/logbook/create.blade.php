@extends('layouts.app')
@section('title', 'Tambah Logbook')

@section('content')
<div class="page-head">
    <div>
        <h1>Tambah Logbook</h1>
        <div class="muted">Kegiatan otomatis dikelompokkan per minggu (Senin–Jumat).</div>
    </div>
</div>
<div class="card">
    @include('logbook._form')
</div>
@endsection
