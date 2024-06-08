<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\FrontendController;
use App\Http\Controllers\api\RoomTypeController;
use App\Http\Controllers\api\RoomNumberController;

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
    Route::controller(RoomNumberController::class)->group(function () {
        Route::post('/room/number/{room_type}/{room_id}', 'createRoomNumber');
        Route::post('/room/number/update/{room_type}/{room_id}/{roomnumber_id}', 'updateRoomNumber');
        Route::delete('/room/number/delete/{roomnumber_id}', 'deleteRoomNumber');
    });

});

Route::controller(FrontendController::class)->group(function () {
    Route::post('/search/available/room/type', 'searchAvailableRoomType');
});




Route::get('/hello', function (Request $request) {
    return response()->json("hello", 200);
});

