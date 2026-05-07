<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TrekController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
