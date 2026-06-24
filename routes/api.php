<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [ApiController::class, 'getUser']);
    Route::post('/inspeksi-sanitasi', [ApiController::class, 'storeInspection']);
});

Route::get('/fasilitas', [ApiController::class, 'index']);
Route::get('/fasilitas/status/{status}', [ApiController::class, 'filterByStatus']);
Route::get('/fasilitas/{id}/inspeksi', [ApiController::class, 'inspectionHistory']);
