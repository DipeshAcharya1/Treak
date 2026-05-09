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

    // Vehicles
    Route::get('vehicles', [\App\Http\Controllers\VehicleController::class, 'index']);
    Route::post('vehicles', [\App\Http\Controllers\VehicleController::class, 'store']);
    Route::get('vehicles/{vehicle}', [\App\Http\Controllers\VehicleController::class, 'show']);

    // Admin-only routes
    Route::middleware('admin')->group(function () {
        Route::get('users', [AuthController::class, 'listUsers']);
    });
});
