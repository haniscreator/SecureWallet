<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\User\MemberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Auth
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    // User / Members
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::get('/members', [MemberController::class, 'index']);
        Route::post('/members', [MemberController::class, 'store']);
        Route::put('/members/{id}', [MemberController::class, 'update']);
        Route::delete('/members/{id}', [MemberController::class, 'destroy']);

        // Wallets
        Route::get('/wallets', [\App\Http\Controllers\Api\V1\Wallet\WalletController::class, 'index']);
        Route::post('/wallets', [\App\Http\Controllers\Api\V1\Wallet\WalletController::class, 'store']);
        Route::get('/wallets/{id}', [\App\Http\Controllers\Api\V1\Wallet\WalletController::class, 'show']);
        Route::put('/wallets/{id}', [\App\Http\Controllers\Api\V1\Wallet\WalletController::class, 'update']);
        Route::post('/wallets/{id}/users', [\App\Http\Controllers\Api\V1\Wallet\WalletController::class, 'assignUser']);
        Route::put('/wallets/{id}/status', [\App\Http\Controllers\Api\V1\Wallet\WalletController::class, 'updateStatus']);

        // Transactions
        Route::post('/transactions/search', [\App\Http\Controllers\Api\V1\Wallet\TransactionController::class, 'all']);
        Route::post('/wallets/{id}/transactions/search', [\App\Http\Controllers\Api\V1\Wallet\TransactionController::class, 'index']);

        // Currencies
        Route::apiResource('currencies', \App\Http\Controllers\Api\V1\Currency\CurrencyController::class);
    });
});