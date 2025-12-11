<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BankApiController;
use App\Http\Controllers\Api\CardApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\TransactionApiController;
use App\Http\Controllers\Api\TransactionChargeApiController;
use App\Http\Controllers\Api\ReportApiController;

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

Route::middleware('validate.api.key')->group(function () {
    
    // Bank endpoints
    Route::prefix('banks')->group(function () {
        Route::get('/', [BankApiController::class, 'index']);
        Route::post('/', [BankApiController::class, 'store']);
        Route::get('/{id}', [BankApiController::class, 'show']);
        Route::put('/{id}', [BankApiController::class, 'update']);
        Route::delete('/{id}', [BankApiController::class, 'destroy']);
    });

    // Card endpoints
    Route::prefix('cards')->group(function () {
        Route::get('/', [CardApiController::class, 'index']);
        Route::post('/', [CardApiController::class, 'store']);
        Route::get('/{id}', [CardApiController::class, 'show']);
        Route::put('/{id}', [CardApiController::class, 'update']);
        Route::delete('/{id}', [CardApiController::class, 'destroy']);
    });

    // Category endpoints
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryApiController::class, 'index']);
        Route::post('/', [CategoryApiController::class, 'store']);
        Route::get('/{id}', [CategoryApiController::class, 'show']);
        Route::put('/{id}', [CategoryApiController::class, 'update']);
        Route::delete('/{id}', [CategoryApiController::class, 'destroy']);
    });

    // Transaction endpoints
    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionApiController::class, 'index']);
        Route::post('/', [TransactionApiController::class, 'store']);
        Route::get('/{id}', [TransactionApiController::class, 'show']);
        Route::put('/{id}', [TransactionApiController::class, 'update']);
        Route::delete('/{id}', [TransactionApiController::class, 'destroy']);

        // Transaction charges nested routes
        Route::prefix('{transactionId}/charges')->group(function () {
            Route::get('/', [TransactionChargeApiController::class, 'index']);
            Route::post('/', [TransactionChargeApiController::class, 'store']);
            Route::get('/{id}', [TransactionChargeApiController::class, 'show']);
            Route::put('/{id}', [TransactionChargeApiController::class, 'update']);
            Route::delete('/{id}', [TransactionChargeApiController::class, 'destroy']);
        });
    });

    // Report endpoints
    Route::prefix('reports')->group(function () {
        Route::get('/summary', [ReportApiController::class, 'summary']);
        Route::get('/by-category', [ReportApiController::class, 'byCategory']);
        Route::get('/monthly-trend', [ReportApiController::class, 'monthlyTrend']);
        Route::get('/by-bank', [ReportApiController::class, 'byBank']);
    });
});
