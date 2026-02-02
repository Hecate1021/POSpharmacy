<?php

use Illuminate\Support\Facades\Route;
// Import all controllers at the top to avoid errors
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Branch\BranchDashboardController;
use App\Http\Controllers\Branch\BranchStaffController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth.login');
});

// ==========================================
// 1. SUPER ADMIN ROUTES
// ==========================================
// Remove .name('admin.') from this line
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // Dashboard -> Now route('admin.dashboard')
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Branch Management -> Now route('admin.branches.index')
    Route::get('/branches', [BranchController::class, 'index'])->name('branches.index');
    Route::post('/branches', [BranchController::class, 'store'])->name('branches.store');
    Route::put('/branches/{branch}', [BranchController::class, 'update'])->name('branches.update');

    // Inventory -> Now route('admin.inventory.index')
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/branch/{branch}', [InventoryController::class, 'showBranch'])->name('inventory.branch');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::put('/inventory/{product}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{product}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    Route::post('/inventory/{product}/quick-add', [InventoryController::class, 'quickAdd'])->name('inventory.quickAdd');

    // Reports -> Now route('admin.reports.index')
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{sale}', [ReportController::class, 'show'])->name('reports.show');
});
// ==========================================
// 2. BRANCH MANAGER ROUTES
// ==========================================
Route::middleware(['auth', 'branch'])->prefix('branch')->name('branch.')->group(function () {

    Route::get('/dashboard', [BranchDashboardController::class, 'index'])->name('dashboard');

    // Inventory (Resource)
    Route::resource('inventory', InventoryController::class)->except(['create', 'show', 'edit']);

    // Staff
    Route::get('/staff', [BranchStaffController::class, 'index'])->name('staff.index');
    Route::post('/staff', [BranchStaffController::class, 'store'])->name('staff.store');
    Route::delete('/staff/{user}', [BranchStaffController::class, 'destroy'])->name('staff.destroy');
});

// ==========================================
// 3. POS TERMINAL ROUTES
// ==========================================
Route::middleware(['auth'])->group(function () {

    // 1. Show the POS Screen
    Route::get('/pos/terminal', [PosController::class, 'index'])->name('pos.terminal');

    // 2. Process the Sale (FIXED: Removed duplicate 'store' line)
    Route::post('/pos/process', [PosController::class, 'store'])->name('pos.process');

    // 3. Load Transaction History
    Route::get('/pos/history', [PosController::class, 'getHistory'])->name('pos.history');
Route::post('/pos/open-register', [PosController::class, 'openRegister'])->name('pos.open_register');
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
