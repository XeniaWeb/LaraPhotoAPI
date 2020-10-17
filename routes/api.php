<?php

use App\Http\Controllers\v1\AlbumController;
use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\AuthorController;
use App\Http\Controllers\v1\PhotoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'registerUserApi']);
Route::post('/login', [AuthController::class, 'loginApi']);
Route::post('/logout', [AuthController::class, 'logoutApi'])->middleware('auth:api');

Route::apiResource('/v1/photos', PhotoController::class);
Route::apiResource('/v1/albums', AlbumController::class);

Route::post('/user/{id}/update', [AuthorController::class, 'update'])->middleware('auth:api');
