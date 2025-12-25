<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PwaController;
use Illuminate\Support\Facades\Route;

// Dashboard Routes
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/asset/{asset}', [DashboardController::class, 'asset'])->name('asset');

// PWA Routes
Route::get('/pwa', [PwaController::class, 'index'])->name('pwa');
Route::get('/manifest.json', [PwaController::class, 'manifest'])->name('pwa.manifest');

// API Routes
Route::prefix('api')->group(function () {
    Route::get('/prices', [ApiController::class, 'prices'])->name('api.prices');
    Route::get('/prices/{asset}/history', [ApiController::class, 'priceHistory'])->name('api.price-history');
    Route::get('/signals', [ApiController::class, 'signals'])->name('api.signals');
    Route::get('/market-overview', [ApiController::class, 'marketOverview'])->name('api.market-overview');
    Route::get('/fear-greed/history', [ApiController::class, 'fearGreedHistory'])->name('api.fear-greed-history');
});
