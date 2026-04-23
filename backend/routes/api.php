<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\CelojumsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\IzdevumsController;
use App\Http\Controllers\RezervacijaController;
use App\Http\Controllers\VietaController;
use App\Http\Controllers\DienasPunktsController;

Route::get('/services', [ServiceController::class, 'index']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/celojumi/stats', [CelojumsController::class, 'stats']);
Route::get('/celojumi', [CelojumsController::class, 'index']);
Route::get('/celojumi/{id}', [CelojumsController::class, 'show']);

Route::get('/vietas', [VietaController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/celojumi', [CelojumsController::class, 'store']);
    Route::put('/celojumi/{id}', [CelojumsController::class, 'update']);
    Route::patch('/celojumi/{id}', [CelojumsController::class, 'update']);
    Route::delete('/celojumi/{id}', [CelojumsController::class, 'destroy']);

    Route::post('/izdevumi', [IzdevumsController::class, 'store']);
    Route::put('/izdevumi/{id}', [IzdevumsController::class, 'update']);
    Route::patch('/izdevumi/{id}', [IzdevumsController::class, 'update']);
    Route::delete('/izdevumi/{id}', [IzdevumsController::class, 'destroy']);

    Route::post('/rezervacijas', [RezervacijaController::class, 'store']);
    Route::put('/rezervacijas/{id}', [RezervacijaController::class, 'update']);
    Route::patch('/rezervacijas/{id}', [RezervacijaController::class, 'update']);
    Route::delete('/rezervacijas/{id}', [RezervacijaController::class, 'destroy']);

    Route::post('/vietas', [VietaController::class, 'store']);

    Route::post('/dienas-punkti', [DienasPunktsController::class, 'store']);
    Route::put('/dienas-punkti/{id}', [DienasPunktsController::class, 'update']);
    Route::patch('/dienas-punkti/{id}', [DienasPunktsController::class, 'update']);
    Route::delete('/dienas-punkti/{id}', [DienasPunktsController::class, 'destroy']);
});