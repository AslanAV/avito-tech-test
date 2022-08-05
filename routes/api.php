<?php

use App\Http\Controllers\BalanceController;
use Illuminate\Support\Facades\Route;

Route::post('/balance/add', [BalanceController::class, 'add'])->name('balance.add');