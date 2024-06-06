<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\RoomTypeController;

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

Route::middleware(['auth:sanctum',])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/auth', [UserController::class, 'show']);
    Route::post('/upload/image', [UserController::class, 'uploadProfilePicture']);
    Route::post('/update/image', [UserController::class, 'updateProfilePicture']);
});

Route::middleware(['auth:sanctum','seller'])->group(function () {
    Route::controller(RoomTypeController::class)->group(function () {
        Route::get('/roomstype', 'index');
        Route::get('/roomstype/{id} ', 'show');
        Route::post('/roomstype/create', 'store');
        Route::post('/roomstype/update/{id}', 'update');
        Route::delete('/roomstype/delete/{id}', 'destroy');
    });
    Route::controller(RoomTypeController::class)->group(function () {
        Route::delete('/roomstype/gallary/delete/{gallaryId}', 'deleteImageGallary');
    });

});




Route::get('/hello', function (Request $request) {
    return response()->json("hello", 200);
});

