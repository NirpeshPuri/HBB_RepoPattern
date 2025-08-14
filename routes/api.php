<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminApiController;
use App\Http\Controllers\ProfileUpdateController;
use App\Http\Controllers\BloodBankController;
use App\Http\Controllers\BloodSearchController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DonorController;

//User Login and register here
Route::post('/login', [LoginController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
Route::post('/logout', [LoginController::class, 'logout']);
});
Route::post('/register', [RegisterController::class, 'register']);

//Now i am check the donor side...
Route::middleware('auth:sanctum')->group(function () {

    // Donation CRUD
    Route::get('donations', [DonorController::class, 'index']);
    Route::get('donations/{id}', [DonorController::class, 'show']);
    Route::post('donations', [DonorController::class, 'store']);
    Route::put('donations/{id}', [DonorController::class, 'update']);
    Route::delete('donations/{id}', [DonorController::class, 'destroy']);

    // Eligibility
    Route::get('donations/eligibility/{id}', [DonorController::class, 'eligibility']);

    // Nearby Admins / Blood Banks
    Route::post('donations/nearby-admins', [DonorController::class, 'nearbyAdmins']);
});

Route::prefix('admin')->group(function () {
    Route::get('/blood-requests/pending', [AdminApiController::class, 'pendingBloodRequests']);
});

// Now for receiver side..
Route::middleware('auth:sanctum')->group(function () {
    // Blood Requests CRUD
    Route::get('/blood-requests', [BloodSearchController::class, 'index']);
    Route::get('/blood-requests/{id}', [BloodSearchController::class, 'show']);
    Route::post('/submit-blood-requests', [BloodSearchController::class, 'store']);
    Route::put('/blood-requests/{id}', [BloodSearchController::class, 'update']);
    Route::delete('/blood-requests/{id}', [BloodSearchController::class, 'destroy']);

    // Nearby Admins
    Route::get('/nearby-admins', [BloodSearchController::class, 'nearbyAdmins']);
});


Route::apiResource('blood-banks', BloodBankController::class);

// Contact Us
Route::post('/contact', [ContactController::class, 'submitForm'])->name('contact.submit');


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileUpdateController::class, 'show']);
    Route::put('/profile', [ProfileUpdateController::class, 'update']);
});
