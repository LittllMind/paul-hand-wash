<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LieuController;
use App\Http\Controllers\Admin\CategorieController;
use App\Http\Controllers\Admin\PresenceController;
use App\Http\Controllers\Front\ReservationController;
use App\Http\Controllers\Front\PaymentController;
use App\Http\Controllers\StripeWebhookController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// ========== Front: Réservations ==========
Route::get('/reserver', [ReservationController::class, 'index'])->name('reserver');
Route::get('/reserver/{presence}', [ReservationController::class, 'show'])->name('reserver.show');
Route::post('/reserver/{presence}', [ReservationController::class, 'store'])->name('reserver.store');
Route::get('/reservation/{reservation}/confirmation', [ReservationController::class, 'confirmation'])->name('reserver.confirmation');

// ========== Front: Paiement Stripe ==========
Route::get('/payment/checkout/{reservation}', [PaymentController::class, 'checkout'])->name('payment.checkout');
Route::post('/payment/checkout-session', [PaymentController::class, 'createCheckoutSession'])->name('payment.checkout-session');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');

// ========== Stripe Webhook ==========
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

// ========== Admin: Lieux ==========
Route::get('/admin/lieux', [LieuController::class, 'index'])->name('admin.lieux.index');
Route::get('/admin/lieux/create', [LieuController::class, 'create'])->name('admin.lieux.create');
Route::post('/admin/lieux', [LieuController::class, 'store'])->name('admin.lieux.store');
Route::get('/admin/lieux/{lieu}/edit', [LieuController::class, 'edit'])->name('admin.lieux.edit');
Route::put('/admin/lieux/{lieu}', [LieuController::class, 'update'])->name('admin.lieux.update');
Route::delete('/admin/lieux/{lieu}', [LieuController::class, 'destroy'])->name('admin.lieux.destroy');

// ========== Admin: Catégories ==========
Route::get('/admin/categories', [CategorieController::class, 'index'])->name('admin.categories.index');
Route::get('/admin/categories/create', [CategorieController::class, 'create'])->name('admin.categories.create');
Route::post('/admin/categories', [CategorieController::class, 'store'])->name('admin.categories.store');
Route::get('/admin/categories/{categorie}/edit', [CategorieController::class, 'edit'])->name('admin.categories.edit');
Route::put('/admin/categories/{categorie}', [CategorieController::class, 'update'])->name('admin.categories.update');
Route::delete('/admin/categories/{categorie}', [CategorieController::class, 'destroy'])->name('admin.categories.destroy');

// ========== Admin: Présences ==========
Route::get('/admin/presences', [PresenceController::class, 'index'])->name('admin.presences.index');
Route::get('/admin/presences/batch', [PresenceController::class, 'createBatch'])->name('admin.presences.batch');
Route::post('/admin/presences/batch', [PresenceController::class, 'storeBatch'])->name('admin.presences.batch');
