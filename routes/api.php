<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\api\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/auth', [UserController::class, 'show']);
    Route::post('/upload/image', [UserController::class, 'uploadProfilePicture']);
    Route::post('/update/image', [UserController::class, 'updateProfilePicture']);
});


Route::get('/hello', function (Request $request) {
    return response()->json("hello", 200);
});

