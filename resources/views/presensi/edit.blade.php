@extends('layouts.app')
@section('title', 'Ubah Presensi')

@section('content')
<div class="page-head"><div><h1>Ubah Presensi</h1></div></div>
<div class="card">
    @include('presensi._form', ['presensi' => $presensi])
</div>
@endsection
