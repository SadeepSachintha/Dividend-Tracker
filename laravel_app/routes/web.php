<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MarketController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/market', [MarketController::class, 'index'])->name('market');
Route::post('/sync', [DashboardController::class, 'sync'])->name('sync');
