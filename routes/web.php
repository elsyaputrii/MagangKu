<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Logbook\ExportLogbookController;
use App\Http\Controllers\Logbook\LogbookController;
use App\Http\Controllers\Presensi\ExportPresensiController;
use App\Http\Controllers\Presensi\PresensiController;
use App\Http\Controllers\Profil\ProfilController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Presensi (export didaftarkan lebih dulu agar tidak bentrok dengan route resource)
Route::get('/presensi/export/excel', [ExportPresensiController::class, 'excel'])->name('presensi.export.excel');
Route::get('/presensi/export/pdf', [ExportPresensiController::class, 'pdf'])->name('presensi.export.pdf');
Route::resource('presensi', PresensiController::class)->except('show');

// Logbook
Route::get('/logbook/export/word', [ExportLogbookController::class, 'word'])->name('logbook.export.word');
Route::get('/logbook/export/pdf', [ExportLogbookController::class, 'pdf'])->name('logbook.export.pdf');
Route::resource('logbook', LogbookController::class)->except('show');

// Profil
Route::get('/profil', [ProfilController::class, 'edit'])->name('profil.edit');
Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
