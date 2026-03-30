<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LieuController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/lieux', [LieuController::class, 'index'])->name('admin.lieux.index');
