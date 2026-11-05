<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('images/logo-polibatam.png') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="topbar">
    <button type="button" id="nav-toggle" aria-label="Buka menu">@include('partials.icon', ['name' => 'menu'])</button>
    <img src="{{ asset('images/logo-polibatam.png') }}" alt="Logo">
    <strong>Presensi &amp; Logbook</strong>
</div>

<div class="app">
    <aside class="sidebar">
        <div class="brand">
            <img src="{{ asset('images/logo-polibatam.png') }}" alt="Logo">
            <div><b>Presensi &amp; Logbook</b><span>Manajemen Magang</span></div>
        </div>

        <nav class="nav">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">@include('partials.icon', ['name' => 'home']) Dashboard</a>
            <a href="{{ route('presensi.index') }}" class="{{ request()->routeIs('presensi.*') ? 'active' : '' }}">@include('partials.icon', ['name' => 'clock']) Presensi</a>
            <a href="{{ route('logbook.index') }}" class="{{ request()->routeIs('logbook.*') ? 'active' : '' }}">@include('partials.icon', ['name' => 'book']) Logbook</a>
            <a href="{{ route('profil.edit') }}" class="{{ request()->routeIs('profil.*') ? 'active' : '' }}">@include('partials.icon', ['name' => 'user']) Profil</a>
        </nav>

        <div class="sidebar-foot">Aplikasi Web Elsya Cantik</div>
    </aside>

    <main class="main">
        @include('partials.alerts')
        @yield('content')
    </main>
</div>
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
