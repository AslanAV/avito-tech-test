<?php

use App\Http\Controllers\BalanceController;
use Illuminate\Support\Facades\Route;

Route::post('/balance/add', [BalanceController::class, 'add'])->name('balance.add');
Route::post('/balance/write-off', [BalanceController::class, 'writeOff'])->name('balance.write-off');