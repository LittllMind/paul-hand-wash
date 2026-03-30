<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LieuController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/lieux', [LieuController::class, 'index'])->name('admin.lieux.index');
Route::get('/admin/lieux/create', [LieuController::class, 'create'])->name('admin.lieux.create');
Route::post('/admin/lieux', [LieuController::class, 'store'])->name('admin.lieux.store');
Route::get('/admin/lieux/{lieu}/edit', [LieuController::class, 'edit'])->name('admin.lieux.edit');
Route::put('/admin/lieux/{lieu}', [LieuController::class, 'update'])->name('admin.lieux.update');
Route::delete('/admin/lieux/{lieu}', [LieuController::class, 'destroy'])->name('admin.lieux.destroy');
