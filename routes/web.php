<?php

use App\Http\Controllers\CalculateController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::get('calculate', [CalculateController::class, 'index'])->name('calculate');
});

require __DIR__.'/settings.php';
