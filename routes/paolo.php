<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\LieuController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Routes admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/lieux', [LieuController::class, 'index'])->name('lieux.index');
});
