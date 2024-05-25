<?php

use App\Http\Controllers\FPController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/check_fingerprint', [FPController::class, 'checkFingerprint']);
Route::post('/finishPage', [FPController::class, 'finishPage']);
Route::post('/getCookie', [FPController::class, 'getCookie']);
Route::post('/check_exist_picture', [FPController::class, 'checkExistPicture']);
Route::post('/pictures', [FPController::class, 'storePictures']);
Route::post('/updateFeatures', [FPController::class, 'updateFeatures']);

