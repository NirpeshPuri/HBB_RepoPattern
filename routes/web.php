<?php

/*
use App\Http\Controllers\ProfileUpdateController;
use App\Http\Controllers\RequestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BloodSearchController;
use App\Http\Controllers\EsewaController;
use App\Http\Controllers\ReceiverStatusController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\BloodBankController;

// Homepage and Static Pages
Route::get('/', function () {
    return view('login');
});

Route::get('/home', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    switch (auth()->user()->user_type) {
        case 'admin':
            return redirect()->route('admin.dashboard');
        case 'donor':
            return redirect()->route('donor.dashboard');
        case 'receiver':
            return redirect()->route('receiver.dashboard');
        default:
            return redirect()->route('login')->with('error', 'Unknown user type');
    }
})->middleware('auth');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/donor_about', function () {
    return view('donor_about');
})->name('donor_about');

Route::get('/contact_us', function () {
    return view('contact_us');
})->name('contact_us');

Route::get('/donor_contact_us', function () {
    return view('donor_contact_us');
})->name('donor_contact_us');

Route::post('/submit-contact-form', [ContactController::class, 'submitForm']);

// Login Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration Routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Admin Routes
Route::middleware(['auth:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/donor-requests', [AdminController::class, 'donorRequests'])->name('admin.donor.requests');
    Route::post('/admin/receiver-requests/{id}/update-status', [AdminController::class, 'updateReceiverStatus'])->name('admin.receiver.update-status');
    Route::post('/admin/donor-requests/{id}/update-status', [AdminController::class, 'updateDonorStatus'])->name('admin.donor.update-status');
    Route::get('/', [UserController::class, 'index'])->name('admin.user_detail');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/request-history', [RequestController::class, 'history'])->name('admin.request_detail');
    Route::get('profile', [AdminController::class, 'showProfileUpdateForm'])->name('admin.profile');
    Route::put('profile/update', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    Route::get('user_report', [AdminController::class, 'report'])->name('admin.user_report');
});

// Receiver Routes
Route::middleware(['auth:web'])->group(function () {
    Route::get('/receiver/dashboard', [UserController::class, 'receiverDashboard'])->name('receiver.dashboard');
});

// Donor Routes
Route::middleware(['auth:web'])->group(function () {
    Route::get('/donor/dashboard', [UserController::class, 'donorDashboard'])->name('donor.dashboard');
});

// Blood Search, Receiver, Donor, Payment, and BloodBank routes (all commented for now)

Route::get('/test', function () {
    return view('test');
});

*/
Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});
use App\Http\Controllers\RegisterController;
Route::post('/register', [RegisterController::class, 'register']);
