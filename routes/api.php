<?php

use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\LicenseController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// License validation and activation endpoints (rate limited + API key auth)
Route::prefix('license')->middleware(['throttle:30,1', \App\Http\Middleware\LicenseApiAuth::class])->group(function () {
    Route::post('/validate', [LicenseController::class, 'validate']);
    Route::post('/activate', [LicenseController::class, 'activate']);
    Route::post('/deactivate', [LicenseController::class, 'deactivate']);
    Route::get('/{license_key}', [LicenseController::class, 'show']);
});

// AI Chatbot endpoints (rate limited)
Route::prefix('chatbot')->middleware('throttle:20,1')->group(function () {
    Route::get('/session', [ChatbotController::class, 'session']);
    Route::post('/send', [ChatbotController::class, 'send']);
    Route::post('/close', [ChatbotController::class, 'close']);
    Route::post('/new', [ChatbotController::class, 'newSession']);
});

// Product API endpoints (rate limited)
Route::prefix('products')->middleware('throttle:60,1')->group(function () {
    Route::get('/{id}', [ProductController::class, 'show'])->where('id', '[0-9]+');
});
