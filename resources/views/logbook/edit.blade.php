@extends('layouts.app')
@section('title', 'Ubah Logbook')

@section('content')
<div class="page-head"><div><h1>Ubah Logbook</h1></div></div>
<div class="card">
    @include('logbook._form', ['logbook' => $logbook])
</div>
@endsection
