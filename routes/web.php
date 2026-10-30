<?php

use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\PerhitunganController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

// CRUD data master
Route::post('kriteria/normalisasi', [KriteriaController::class, 'normalisasi'])->name('kriteria.normalisasi');
Route::resource('kriteria', KriteriaController::class)
    ->except('show')->parameters(['kriteria' => 'kriteria']);
Route::resource('alternatif', AlternatifController::class)
    ->except('show')->parameters(['alternatif' => 'alternatif']);

// Penilaian (matriks keputusan)
Route::get('penilaian', [PenilaianController::class, 'index'])->name('penilaian.index');
Route::get('penilaian/{alternatif}/edit', [PenilaianController::class, 'edit'])->name('penilaian.edit');
Route::put('penilaian/{alternatif}', [PenilaianController::class, 'update'])->name('penilaian.update');
Route::delete('penilaian/{alternatif}', [PenilaianController::class, 'destroy'])->name('penilaian.destroy');

// Perhitungan SAW & hasil
Route::get('perhitungan', [PerhitunganController::class, 'index'])->name('perhitungan');
Route::get('hasil', [PerhitunganController::class, 'hasil'])->name('hasil');
