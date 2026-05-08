<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TrekController;
use Illuminate\Support\Facades\Route;

Route::get('health', function () {
    return response()->json(['status' => 'ok']);
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('me', [AuthController::class, 'me']);
Route::post('logout', [AuthController::class, 'logout']);
Route::put('profile', [AuthController::class, 'updateProfile']);
Route::middleware('admin')->get('users', [AuthController::class, 'listUsers']);

Route::get('treks', [TrekController::class, 'index']);
Route::post('treks', [TrekController::class, 'store']);
Route::get('treks/{trek}', [TrekController::class, 'show']);
Route::put('treks/{trek}', [TrekController::class, 'update']);
Route::delete('treks/{trek}', [TrekController::class, 'destroy']);
