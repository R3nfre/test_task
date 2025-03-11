<?php

use App\Http\Controllers\CurrencyController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['custom.auth'])->group(function () {
    Route::any('/', [CurrencyController::class, 'handleRequest']);
});
