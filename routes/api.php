<?php

use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\BloodSearchController;
use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/register', [\App\Http\Controllers\Api\RegisterController::class, 'register']);
// routes/api.php
Route::prefix('admin')->group(function() {
    Route::get('/blood-requests/pending', [AdminApiController::class, 'pendingBloodRequests']);
});

Route::middleware('auth:sanctum')->group(function() {
    Route::prefix('blood')->group(function() {
        // Find nearby blood banks
        Route::post('/nearby', [BloodSearchController::class, 'findNearbyAdmins']);

        // Submit blood request
        Route::post('/request', [BloodSearchController::class, 'submitRequest']);

        // Process payment
        Route::post('/payment/process', [BloodSearchController::class, 'processPayment']);

        // Payment success callback
        Route::get('/payment/success', [BloodSearchController::class, 'paymentSuccess']);

        // Payment failure callback
        Route::get('/payment/failure', [BloodSearchController::class, 'paymentFailure']);
    });
});

use App\Http\Controllers\BloodBankController;
Route::apiResource('blood-banks', BloodBankController::class);

use App\Http\Controllers\ContactController;

// If you want RESTful API endpoint
Route::post('/contact', [ContactController::class, 'submitForm'])->name('contact.submit');

use App\Http\Controllers\Api\ProfileUpdateController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileUpdateController::class, 'show']);
    Route::put('/profile', [ProfileUpdateController::class, 'update']);
});

