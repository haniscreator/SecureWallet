<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\User\MemberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// User / Members
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/members', [MemberController::class, 'index']);
    Route::get('/members/{id}', [MemberController::class, 'show']);
    Route::post('/members', [MemberController::class, 'store']);
    Route::put('/members/{id}', [MemberController::class, 'update']);
    Route::delete('/members/{id}', [MemberController::class, 'destroy']);

    // Wallets
    Route::get('/wallets', [\App\Http\Controllers\Api\Wallet\WalletController::class, 'index']);
    Route::post('/wallets', [\App\Http\Controllers\Api\Wallet\WalletController::class, 'store']);
    Route::get('/wallets/{id}', [\App\Http\Controllers\Api\Wallet\WalletController::class, 'show']);
    Route::put('/wallets/{id}', [\App\Http\Controllers\Api\Wallet\WalletController::class, 'update']);
    Route::post('/wallets/{id}/users', [\App\Http\Controllers\Api\Wallet\WalletController::class, 'assignUser']);
    Route::put('/wallets/{id}/status', [\App\Http\Controllers\Api\Wallet\WalletController::class, 'updateStatus']);

    // Transactions
    Route::post('/transactions/search', [\App\Http\Controllers\Api\Wallet\TransactionController::class, 'all']);
    Route::get('/transactions/{id}', [\App\Http\Controllers\Api\Wallet\TransactionController::class, 'show']);
    Route::post('/wallets/{id}/transactions/search', [\App\Http\Controllers\Api\Wallet\TransactionController::class, 'index']);

    // Currencies
    Route::apiResource('currencies', \App\Http\Controllers\Api\Currency\CurrencyController::class);
});