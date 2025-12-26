<?php

use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\LicenseController;
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

// License validation and activation endpoints (public)
Route::prefix('license')->group(function () {
    Route::post('/validate', [LicenseController::class, 'validate']);
    Route::post('/activate', [LicenseController::class, 'activate']);
    Route::post('/deactivate', [LicenseController::class, 'deactivate']);
    Route::get('/{license_key}', [LicenseController::class, 'show']);
});

// AI Chatbot endpoints (public with rate limiting)
Route::prefix('chatbot')->group(function () {
    Route::get('/session', [ChatbotController::class, 'session']);
    Route::post('/send', [ChatbotController::class, 'send']);
    Route::post('/close', [ChatbotController::class, 'close']);
    Route::post('/new', [ChatbotController::class, 'newSession']);
});
