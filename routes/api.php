<?php
use App\Http\Controllers\BalanceController;
use Illuminate\Support\Facades\Route;

Route::post('/users/{user}/balance/add', [BalanceController::class, 'add'])
    ->name('balance.add');
Route::post('/users/{user}/balance/write-off', [BalanceController::class, 'writeOff'])
    ->name('balance.write_off');
Route::get('/users/{user}/balance', [BalanceController::class, 'show'])
    ->name('balance.show');
Route::post('/users/{user1}/{user2}/balance/transaction', [BalanceController::class, 'transaction'])
    ->name('balance.transaction');