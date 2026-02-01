<?php

use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Branch\BranchDashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

// ==========================================
// 1. SUPER ADMIN ROUTES (Head Office)
// ==========================================
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // ------------------------------------------
    // BRANCH & USER MANAGEMENT
    // ------------------------------------------
    Route::get('/branches', [BranchController::class, 'index'])->name('branches.index');
    Route::post('/branches', [BranchController::class, 'store'])->name('branches.store');
    Route::put('/branches/{branch}', [BranchController::class, 'update'])->name('branches.update');

    // Staff / User CRUD (Explicit definition works best here)
    Route::post('/users', [BranchController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}', [BranchController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [BranchController::class, 'destroyUser'])->name('users.destroy');


    // Inside your existing 'admin' middleware group
Route::get('/reports/{sale}', [App\Http\Controllers\Admin\ReportController::class, 'show'])->name('reports.show');
    // ------------------------------------------
    // INVENTORY MANAGEMENT (Fixed Conflicts)
    // ------------------------------------------
    // Note: I removed Route::resource() because we are defining custom logic below.
    // This ensures the parameter "{product}" matches your Controller's "Product $product"

    // 1. Main Inventory Page (Branch Selector)
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');

    // 2. Specific Branch Inventory (The Table)
    Route::get('/inventory/branch/{branch}', [InventoryController::class, 'showBranch'])->name('inventory.branch');

    // 3. CRUD Actions
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::put('/inventory/{product}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{product}', [InventoryController::class, 'destroy'])->name('inventory.destroy');

    // Quick Stock Add
    Route::post('/inventory/{product}/quick-add', [InventoryController::class, 'quickAdd'])->name('inventory.quickAdd');

    // ------------------------------------------
    // REPORTS
    // ------------------------------------------
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

// ==========================================
// 2. BRANCH MANAGER ROUTES
// ==========================================
Route::middleware(['auth', 'branch'])->prefix('branch')->group(function () {

    // Dedicated Branch Dashboard
    Route::get('/dashboard', [BranchDashboardController::class, 'index'])->name('branch.dashboard');

    // Branch Inventory (Resource is fine here if standard CRUD is used,
    // but ensure your Branch Manager views use the correct route names)
    Route::resource('inventory', InventoryController::class)->except(['create', 'show', 'edit']);

    // Branch Staff Management
    Route::get('/staff', [\App\Http\Controllers\Branch\BranchStaffController::class, 'index'])->name('branch.staff.index');
    Route::post('/staff', [\App\Http\Controllers\Branch\BranchStaffController::class, 'store'])->name('branch.staff.store');
    Route::delete('/staff/{user}', [\App\Http\Controllers\Branch\BranchStaffController::class, 'destroy'])->name('branch.staff.destroy');
});

// ==========================================
// 3. POS & SHARED ROUTES
// ==========================================
Route::middleware(['auth'])->group(function () {

    // POS Terminal
    Route::get('/pos/terminal', [PosController::class, 'index'])->name('pos.terminal');
    Route::post('/pos/process', [PosController::class, 'store'])->name('pos.process');

    // User Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Inside your 'auth' or 'cashier' middleware group
Route::get('/pos/history', [App\Http\Controllers\PosController::class, 'getHistory'])->name('pos.history');
});

require __DIR__.'/auth.php';
