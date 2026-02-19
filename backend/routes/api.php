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
        return new \App\Domain\User\Resources\UserResource($request->user()->load('role'));
    });

    Route::get('/members', [MemberController::class, 'index']);
    Route::get('/members/{id}', [MemberController::class, 'show']);
    Route::post('/members', [MemberController::class, 'store']);
    Route::put('/members/{id}', [MemberController::class, 'update']);
    Route::delete('/members/{id}', [MemberController::class, 'destroy']);

    // Wallets
    Route::get('/dashboard/widget', [\App\Http\Controllers\Api\Wallet\WalletController::class, 'dashboardWidget']);
    Route::get('/wallets/validate-address', [\App\Http\Controllers\Api\Wallet\WalletController::class, 'validateAddress']);
    Route::get('/wallets/transfer-targets', [\App\Http\Controllers\Api\Wallet\WalletController::class, 'transferTargets']);
    Route::get('/wallets', [\App\Http\Controllers\Api\Wallet\WalletController::class, 'index']);
    Route::post('/wallets', [\App\Http\Controllers\Api\Wallet\WalletController::class, 'store']);
    Route::get('/wallets/{id}', [\App\Http\Controllers\Api\Wallet\WalletController::class, 'show']);
    Route::put('/wallets/{id}', [\App\Http\Controllers\Api\Wallet\WalletController::class, 'update']);
    Route::post('/wallets/{id}/users', [\App\Http\Controllers\Api\Wallet\WalletController::class, 'assignUser']);
    Route::put('/wallets/{id}/status', [\App\Http\Controllers\Api\Wallet\WalletController::class, 'updateStatus']);

    // Transactions
    Route::post('/transactions/search', [\App\Http\Controllers\Api\Wallet\TransactionController::class, 'all']);
    Route::get('/transactions/dashboard-total-balance', [\App\Http\Controllers\Api\Wallet\TransactionController::class, 'dashboardTotalBalance']);
    Route::get('/transactions/{id}', [\App\Http\Controllers\Api\Wallet\TransactionController::class, 'show']);
    Route::post('/wallets/{id}/transactions/search', [\App\Http\Controllers\Api\Wallet\TransactionController::class, 'index']);

    // Currencies
    Route::apiResource('currencies', \App\Http\Controllers\Api\Currency\CurrencyController::class);

    // Transfers
    Route::post('/transfers', [\App\Http\Controllers\Api\Transaction\TransferController::class, 'initiate']);
    Route::post('/transfers/{transaction}/approve', [\App\Http\Controllers\Api\Transaction\TransferController::class, 'approve']);
    Route::post('/transfers/{transaction}/reject', [\App\Http\Controllers\Api\Transaction\TransferController::class, 'reject']);
    Route::post('/transfers/{transaction}/cancel', [\App\Http\Controllers\Api\Transaction\TransferController::class, 'cancel']);

    // External Wallets
    Route::get('/external-wallets', [\App\Http\Controllers\Api\Wallet\ExternalWalletController::class, 'index']);

    // Settings
    Route::get('/settings', [\App\Http\Controllers\Api\Setting\SettingController::class, 'index']);
    Route::post('/settings', [\App\Http\Controllers\Api\Setting\SettingController::class, 'update']);
});