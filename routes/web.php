<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});


Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', action: [DashboardController::class, 'index'])->name('admin.dashboard');

    // Inventory Routes
    Route::resource('inventory', InventoryController::class)->except(['create', 'show', 'edit']);

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});


// Update the existing POS route group
Route::middleware(['auth'])->group(function () {
    // ... existing routes ...

    // POS Terminal Route
    Route::get('/pos/terminal', [PosController::class, 'index'])->name('pos.terminal');
    Route::post('/pos/process', [PosController::class, 'store'])->name('pos.process');
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
