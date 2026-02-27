<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ProfileController;
use App\Http\Controllers\Api\Catalog\PortController;
use App\Http\Controllers\Api\Catalog\BerthController;
use App\Http\Controllers\Api\Catalog\SearchController;
use App\Http\Controllers\Api\Catalog\StatsController;
use App\Http\Controllers\Api\Booking\BookingController;
use App\Http\Controllers\Api\Booking\AvailabilityController;
use App\Http\Controllers\Api\Rating\ReviewController;
use App\Http\Controllers\Api\Rating\SelfAssessmentController;
use App\Http\Controllers\Api\Rating\CertificationController;
use App\Http\Controllers\Api\Wallet\WalletController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\PortController as AdminPortController;
use App\Http\Controllers\Api\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Api\Admin\RatingController as AdminRatingController;
use App\Http\Controllers\Api\Admin\BerthController as AdminBerthController;
use App\Http\Controllers\Api\Admin\ConventionController as AdminConventionController;
use App\Http\Controllers\Api\Admin\FinancialController as AdminFinancialController;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', [RegisterController::class, 'register']);

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [LoginController::class, 'logout']);
        Route::post('refresh', [LoginController::class, 'refresh']);
        Route::get('profile', [ProfileController::class, 'show']);
        Route::put('profile', [ProfileController::class, 'update']);
        Route::put('password', [ProfileController::class, 'updatePassword']);
        Route::post('avatar', [ProfileController::class, 'uploadAvatar']);
    });
});

/*
|--------------------------------------------------------------------------
| Catalog Routes (Public)
|--------------------------------------------------------------------------
*/

Route::prefix('catalog')->group(function () {
    Route::get('countries', [PortController::class, 'countries']);
    Route::get('regions', [PortController::class, 'regions']);
    Route::get('ports', [PortController::class, 'index']);
    Route::get('ports/{port}', [PortController::class, 'show']);
    Route::get('berths/top', [BerthController::class, 'top']);
    Route::get('berths/latest', [BerthController::class, 'latest']);
    Route::get('berths/search', SearchController::class);
    Route::get('berths/{berth}', [BerthController::class, 'show']);
    Route::get('stats', StatsController::class);
});

/*
|--------------------------------------------------------------------------
| Owner Routes
|--------------------------------------------------------------------------
*/

Route::prefix('owner')->middleware(['auth:api', 'role:owner'])->group(function () {
    Route::get('dashboard', [BookingController::class, 'ownerDashboard']);

    // Berth management
    Route::get('berths', [BerthController::class, 'ownerIndex']);
    Route::post('berths', [BerthController::class, 'store']);
    Route::get('berths/{berth}', [BerthController::class, 'ownerShow']);
    Route::put('berths/{berth}', [BerthController::class, 'update']);
    Route::delete('berths/{berth}', [BerthController::class, 'destroy']);
    Route::post('berths/{berth}/images', [BerthController::class, 'uploadImages']);

    // Availability
    Route::get('berths/{berth}/availability', [AvailabilityController::class, 'index']);
    Route::post('berths/{berth}/availability', [AvailabilityController::class, 'store']);

    // Bookings
    Route::get('berths/{berth}/bookings', [BookingController::class, 'ownerBerthBookings']);
    Route::put('bookings/{booking}/confirm', [BookingController::class, 'confirm']);
    Route::put('bookings/{booking}/reject', [BookingController::class, 'reject']);

    // Self-assessment
    Route::get('berths/{berth}/assessment', [SelfAssessmentController::class, 'show']);
    Route::post('berths/{berth}/assessment', [SelfAssessmentController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::prefix('guest')->middleware(['auth:api', 'role:guest,owner'])->group(function () {
    Route::get('dashboard', [BookingController::class, 'guestDashboard']);
    Route::get('bookings', [BookingController::class, 'index']);
    Route::post('bookings', [BookingController::class, 'store']);
    Route::get('bookings/{booking}', [BookingController::class, 'show']);
    Route::put('bookings/{booking}/cancel', [BookingController::class, 'cancel']);
    Route::post('bookings/{booking}/review', [ReviewController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| Wallet Routes (Authenticated)
|--------------------------------------------------------------------------
*/

Route::prefix('wallet')->middleware('auth:api')->group(function () {
    Route::get('/', [WalletController::class, 'index']);
    Route::get('transactions', [WalletController::class, 'transactions']);
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth:api', 'role:admin'])->group(function () {
    Route::get('dashboard', [AdminRatingController::class, 'dashboard']);

    // Users
    Route::get('users', [AdminUserController::class, 'index']);
    Route::put('users/{user}/role', [AdminUserController::class, 'updateRole']);

    // Ports
    Route::get('ports', [AdminPortController::class, 'index']);
    Route::post('ports', [AdminPortController::class, 'store']);
    Route::get('ports/{port}', [AdminPortController::class, 'show']);
    Route::put('ports/{port}', [AdminPortController::class, 'update']);
    Route::get('ports/{port}/conventions', [AdminConventionController::class, 'byPort']);

    // Berths
    Route::get('berths', [AdminBerthController::class, 'index']);
    Route::get('berths/{berth}', [AdminBerthController::class, 'show']);
    Route::put('berths/{berth}/toggle-active', [AdminBerthController::class, 'toggleActive']);

    // Bookings & Transactions
    Route::get('bookings', [AdminBookingController::class, 'index']);
    Route::get('transactions', [AdminBookingController::class, 'transactions']);

    // Ratings & Certifications
    Route::get('ratings', [AdminRatingController::class, 'index']);
    Route::get('certifications', [CertificationController::class, 'index']);

    // Conventions
    Route::get('conventions/categories', [AdminConventionController::class, 'categories']);
    Route::get('conventions', [AdminConventionController::class, 'index']);
    Route::post('conventions', [AdminConventionController::class, 'store']);
    Route::get('conventions/{convention}', [AdminConventionController::class, 'show']);
    Route::put('conventions/{convention}', [AdminConventionController::class, 'update']);
    Route::delete('conventions/{convention}', [AdminConventionController::class, 'destroy']);

    // Financial
    Route::prefix('financial')->group(function () {
        Route::get('overview', [AdminFinancialController::class, 'overview']);
        Route::get('transactions', [AdminFinancialController::class, 'transactions']);
        Route::get('revenue-by-port', [AdminFinancialController::class, 'revenueByPort']);
        Route::get('revenue-by-period', [AdminFinancialController::class, 'revenueByPeriod']);
    });
});
