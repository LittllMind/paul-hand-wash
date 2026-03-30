<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LieuController;
use App\Http\Controllers\Admin\PresenceController;
use App\Http\Controllers\Front\ReservationController;

Route::get('/', function () {
    return view('welcome');
});

// ========== Front: Réservations ==========
Route::get('/reserver', [ReservationController::class, 'index'])->name('reserver');
Route::get('/reserver/{presence}', [ReservationController::class, 'show'])->name('reserver.show');

// ========== Admin: Lieux ==========
Route::get('/admin/lieux', [LieuController::class, 'index'])->name('admin.lieux.index');
Route::get('/admin/lieux/create', [LieuController::class, 'create'])->name('admin.lieux.create');
Route::post('/admin/lieux', [LieuController::class, 'store'])->name('admin.lieux.store');
Route::get('/admin/lieux/{lieu}/edit', [LieuController::class, 'edit'])->name('admin.lieux.edit');
Route::put('/admin/lieux/{lieu}', [LieuController::class, 'update'])->name('admin.lieux.update');
Route::delete('/admin/lieux/{lieu}', [LieuController::class, 'destroy'])->name('admin.lieux.destroy');

// ========== Admin: Présences ==========
Route::get('/admin/presences', [PresenceController::class, 'index'])->name('admin.presences.index');
Route::get('/admin/presences/batch', [PresenceController::class, 'createBatch'])->name('admin.presences.batch');
Route::post('/admin/presences/batch', [PresenceController::class, 'storeBatch'])->name('admin.presences.batch');
