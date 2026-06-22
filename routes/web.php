<?php

use App\Http\Controllers\CalculateController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::get('calculate', [CalculateController::class, 'index'])->name('calculate');
    Route::get('calculate/clients/search', [CalculateController::class, 'searchClients'])
        ->name('calculate.clients.search');
    Route::post('calculate/client-step', [CalculateController::class, 'resolveClientStep'])
        ->name('calculate.client-step.resolve');
});

require __DIR__.'/settings.php';
