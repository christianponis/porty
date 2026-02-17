<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Guest;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Owner;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// --- Pubblica ---
Route::get('/', HomeController::class);

Route::get('/search', Guest\SearchController::class)->name('search');
Route::get('/berths/{berth}', [Guest\SearchController::class, 'show'])->name('berths.show');

// --- Autenticato ---
Route::middleware('auth')->group(function () {

    // Dashboard generica (redirect basato su ruolo)
    Route::get('/dashboard', function () {
        return match (auth()->user()->role->value) {
            'admin' => redirect()->route('admin.dashboard'),
            'owner' => redirect()->route('owner.dashboard'),
            'guest' => redirect()->route('guest.dashboard'),
        };
    })->name('dashboard');

    // Profilo
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Prenotazioni
    Route::post('/bookings', [Guest\BookingController::class, 'store'])->name('bookings.store');
    Route::get('/my-bookings', [Guest\BookingController::class, 'index'])->name('my-bookings');
    Route::get('/my-bookings/{booking}', [Guest\BookingController::class, 'show'])->name('my-bookings.show');
    Route::put('/my-bookings/{booking}/cancel', [Guest\BookingController::class, 'cancel'])->name('my-bookings.cancel');

    // Recensioni
    Route::get('/my-bookings/{booking}/review', [Guest\ReviewController::class, 'create'])->name('my-bookings.review');
    Route::post('/my-bookings/{booking}/review', [Guest\ReviewController::class, 'store'])->name('my-bookings.review.store');
});

// --- Admin ---
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', Admin\DashboardController::class)->name('dashboard');

    Route::get('/users', [Admin\UserController::class, 'index'])->name('users');
    Route::put('/users/{user}/toggle', [Admin\UserController::class, 'toggleActive'])->name('users.toggle');

    Route::get('/ports', [Admin\PortController::class, 'index'])->name('ports');
    Route::get('/ports/create', [Admin\PortController::class, 'create'])->name('ports.create');
    Route::post('/ports', [Admin\PortController::class, 'store'])->name('ports.store');
    Route::get('/ports/{port}/edit', [Admin\PortController::class, 'edit'])->name('ports.edit');
    Route::put('/ports/{port}', [Admin\PortController::class, 'update'])->name('ports.update');

    Route::get('/transactions', [Admin\TransactionController::class, 'index'])->name('transactions');

    Route::get('/ratings', [Admin\RatingController::class, 'index'])->name('ratings');
    Route::get('/certifications', [Admin\RatingController::class, 'certifications'])->name('certifications');
});

// --- Owner ---
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', Owner\DashboardController::class)->name('dashboard');

    Route::get('/berths', [Owner\BerthController::class, 'index'])->name('berths.index');
    Route::get('/berths/create', [Owner\BerthController::class, 'create'])->name('berths.create');
    Route::post('/berths', [Owner\BerthController::class, 'store'])->name('berths.store');
    Route::get('/berths/{berth}', [Owner\BerthController::class, 'show'])->name('berths.show');
    Route::get('/berths/{berth}/edit', [Owner\BerthController::class, 'edit'])->name('berths.edit');
    Route::put('/berths/{berth}', [Owner\BerthController::class, 'update'])->name('berths.update');
    Route::delete('/berths/{berth}', [Owner\BerthController::class, 'destroy'])->name('berths.destroy');

    Route::post('/berths/{berth}/availability', [Owner\AvailabilityController::class, 'store'])->name('availability.store');
    Route::delete('/berths/{berth}/availability/{availability}', [Owner\AvailabilityController::class, 'destroy'])->name('availability.destroy');

    Route::get('/bookings', [Owner\BookingController::class, 'index'])->name('bookings');
    Route::put('/bookings/{booking}', [Owner\BookingController::class, 'updateStatus'])->name('bookings.update');

    // Self-Assessment
    Route::get('/berths/{berth}/assessment', [Owner\SelfAssessmentController::class, 'show'])->name('assessment.show');
    Route::post('/berths/{berth}/assessment', [Owner\SelfAssessmentController::class, 'store'])->name('assessment.store');

    // Nodi Wallet
    Route::get('/nodi', [Owner\NodiController::class, 'index'])->name('nodi');
});

// --- Guest ---
Route::middleware(['auth', 'role:guest,owner'])->prefix('guest')->name('guest.')->group(function () {
    Route::get('/dashboard', Guest\DashboardController::class)->name('dashboard');

    // Nodi Wallet
    Route::get('/nodi', [Guest\NodiController::class, 'index'])->name('nodi');
});

require __DIR__.'/auth.php';
