<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TrekController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (no authentication required)
|--------------------------------------------------------------------------
*/

Route::get('health', function () {
    return response()->json(['status' => 'ok']);
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Public Trek Discovery
Route::get('treks/public', [TrekController::class, 'listAll']);
Route::get('treks/public/{trek}', [TrekController::class, 'showPublic']);

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Bearer token required)
|--------------------------------------------------------------------------
*/

Route::middleware('auth.api')->group(function () {
    // User profile
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::put('profile', [AuthController::class, 'updateProfile']);

    // Treks CRUD
    Route::get('treks', [TrekController::class, 'index']);
    Route::post('treks', [TrekController::class, 'store']);
    Route::get('treks/{trek}', [TrekController::class, 'show']);
    Route::put('treks/{trek}', [TrekController::class, 'update']);
    Route::delete('treks/{trek}', [TrekController::class, 'destroy']);

    // Bookings
    Route::get('bookings', [\App\Http\Controllers\BookingController::class, 'index']);
    Route::post('bookings', [\App\Http\Controllers\BookingController::class, 'store']);
    Route::get('bookings/{booking}', [\App\Http\Controllers\BookingController::class, 'show']);
    Route::put('bookings/{booking}/cancel', [\App\Http\Controllers\BookingController::class, 'cancel']);
    Route::post('bookings/{id}/pay', [\App\Http\Controllers\BookingController::class, 'processPayment']);

    // Reviews
    Route::get('treks/{trek}/reviews', [\App\Http\Controllers\ReviewController::class, 'index']);
    Route::post('reviews', [\App\Http\Controllers\ReviewController::class, 'store']);

    // Itineraries
    Route::get('treks/{trek}/itineraries', [\App\Http\Controllers\ItineraryController::class, 'index']);
    Route::post('treks/{trek}/itineraries', [\App\Http\Controllers\ItineraryController::class, 'store']);

    // Guides
    Route::get('guides', [\App\Http\Controllers\GuideController::class, 'index']);
    Route::post('guides', [\App\Http\Controllers\GuideController::class, 'store']);
    Route::get('guides/{guide}', [\App\Http\Controllers\GuideController::class, 'show']);
    Route::delete('guides/{guide}', [\App\Http\Controllers\GuideController::class, 'destroy']);

    // Vehicles
    Route::get('vehicles', [\App\Http\Controllers\VehicleController::class, 'index']);
    Route::post('vehicles', [\App\Http\Controllers\VehicleController::class, 'store']);
    Route::get('vehicles/{vehicle}', [\App\Http\Controllers\VehicleController::class, 'show']);
    Route::delete('vehicles/{vehicle}', [\App\Http\Controllers\VehicleController::class, 'destroy']);

    // Admin-only routes
    Route::middleware('admin')->group(function () {
        Route::get('users', [AuthController::class, 'listUsers']);
        Route::put('users/{user}', [AuthController::class, 'updateUser']);
        Route::delete('users/{user}', [AuthController::class, 'deleteUser']);
        
        // Admin Bookings
        Route::get('admin/bookings', [\App\Http\Controllers\BookingController::class, 'listAll']);
        Route::put('admin/bookings/{id}/status', [\App\Http\Controllers\BookingController::class, 'updateStatus']);

        // Admin Reviews
        Route::get('admin/reviews', [\App\Http\Controllers\ReviewController::class, 'listAll']);
        Route::delete('admin/reviews/{id}', [\App\Http\Controllers\ReviewController::class, 'destroy']);
    });
});
