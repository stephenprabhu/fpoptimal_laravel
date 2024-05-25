<?php

use App\Http\Controllers\FPController;
use Illuminate\Support\Facades\Route;

Route::get('/check_fingerprint', [FPController::class, 'checkFingerprint']);
Route::post('/finishPage', [FPController::class, 'finishPage']);
Route::post('/getCookie', [FPController::class, 'getCookie']);
Route::post('/check_exist_picture', [FPController::class, 'checkExistPicture']);
Route::post('/pictures', [FPController::class, 'storePictures']);
Route::post('/updateFeatures', [FPController::class, 'updateFeatures']);
