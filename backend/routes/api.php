<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceController;

Route::prefix('api')->group(function () {
    Route::get('/services', [ServiceController::class, 'index']);
});

