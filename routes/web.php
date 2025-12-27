<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PinAuthController;
use App\Http\Controllers\Auth\QrAuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\PwaController;
use App\Http\Controllers\WebAuthn\WebAuthnLoginController;
use App\Http\Controllers\WebAuthn\WebAuthnRegisterController;
use Illuminate\Support\Facades\Route;

// Auth Routes (public)
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration (public)
Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// PIN Auth (public)
Route::post('/auth/pin/check-device', [PinAuthController::class, 'checkDevice']);
Route::post('/auth/pin/login', [PinAuthController::class, 'loginWithPin']);

// QR Auth (public)
Route::get('/auth/qr/generate', [QrAuthController::class, 'generate']);
Route::get('/auth/qr/{token}/status', [QrAuthController::class, 'status']);

// WebAuthn/Passkey (public)
Route::post('/auth/passkey/login/options', [WebAuthnLoginController::class, 'options']);
Route::post('/auth/passkey/login', [WebAuthnLoginController::class, 'login']);

// Auth required routes
Route::middleware(['auth'])->group(function () {
    // PIN Setup
    Route::get('/auth/setup-pin', [LoginController::class, 'setupPin'])->name('auth.setup-pin');
    Route::post('/auth/pin/setup', [PinAuthController::class, 'setupPin']);
    Route::post('/auth/pin/biometric', [PinAuthController::class, 'enableBiometric']);
    Route::post('/auth/pin/remove', [PinAuthController::class, 'removeDevice']);

    // QR Approve (from smartphone)
    Route::get('/auth/qr/scan', [QrAuthController::class, 'scan'])->name('auth.qr-scan');
    Route::post('/auth/qr/approve', [QrAuthController::class, 'approve']);

    // WebAuthn Register
    Route::post('/auth/passkey/register/options', [WebAuthnRegisterController::class, 'options']);
    Route::post('/auth/passkey/register', [WebAuthnRegisterController::class, 'register']);

    // Dashboard Routes (protected)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/asset/{asset}', [DashboardController::class, 'asset'])->name('asset');

    // Portfolio Routes (Bitvavo)
    Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');
    Route::get('/portfolio/setup', [PortfolioController::class, 'setup'])->name('portfolio.setup');
    Route::post('/portfolio/credentials', [PortfolioController::class, 'storeCredentials'])->name('portfolio.credentials.store');
    Route::post('/portfolio/sync', [PortfolioController::class, 'sync'])->name('portfolio.sync');
    Route::get('/portfolio/transactions', [PortfolioController::class, 'transactions'])->name('portfolio.transactions');
    Route::delete('/portfolio/disconnect', [PortfolioController::class, 'disconnect'])->name('portfolio.disconnect');
});

// PWA Routes
Route::get('/pwa', [PwaController::class, 'index'])->name('pwa');
Route::get('/manifest.json', [PwaController::class, 'manifest'])->name('pwa.manifest');

// API Routes
Route::prefix('api')->group(function () {
    // Public API routes
    Route::get('/prices', [ApiController::class, 'prices'])->name('api.prices');
    Route::get('/prices/{asset}/history', [ApiController::class, 'priceHistory'])->name('api.price-history');
    Route::get('/signals', [ApiController::class, 'signals'])->name('api.signals');
    Route::get('/market-overview', [ApiController::class, 'marketOverview'])->name('api.market-overview');
    Route::get('/fear-greed/history', [ApiController::class, 'fearGreedHistory'])->name('api.fear-greed-history');
    Route::get('/whale-alerts', [ApiController::class, 'whaleAlerts'])->name('api.whale-alerts');

    // Protected API routes (for PWA)
    Route::middleware(['auth'])->group(function () {
        Route::get('/portfolio', [ApiController::class, 'portfolio'])->name('api.portfolio');
        Route::post('/portfolio/sync', [ApiController::class, 'portfolioSync'])->name('api.portfolio.sync');
    });
});
