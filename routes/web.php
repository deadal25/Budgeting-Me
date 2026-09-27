<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// Public Landing Page & Real-Time Form
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/check-email', [LandingController::class, 'checkEmail'])->name('check.email');
Route::get('/api/categories', [LandingController::class, 'getCategories'])->name('api.categories');
Route::post('/public-transaction', [LandingController::class, 'storePublicTransaction'])->name('public.transaction.store');

// Authenticated User Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Transactions CRUD & PDF Export
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/export/pdf', [TransactionController::class, 'exportPdf'])->name('transactions.export.pdf');
    Route::get('/transactions/{transaction}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
    Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

    // Budgetinku (Limit Anggaran & Notifikasi)
    Route::get('/budgets', [BudgetController::class, 'index'])->name('budgets.index');
    Route::post('/budgets', [BudgetController::class, 'store'])->name('budgets.store');
    Route::put('/budgets/{budget}', [BudgetController::class, 'update'])->name('budgets.update');
    Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy'])->name('budgets.destroy');

    // Catatan Utang & Pembayaran
    Route::get('/debts', [DebtController::class, 'index'])->name('debts.index');
    Route::post('/debts', [DebtController::class, 'store'])->name('debts.store');
    Route::put('/debts/{debt}', [DebtController::class, 'update'])->name('debts.update');
    Route::delete('/debts/{debt}', [DebtController::class, 'destroy'])->name('debts.destroy');
    Route::post('/debts/{debt}/pay', [DebtController::class, 'pay'])->name('debts.pay');

    // User Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Panel (Protected by Auth and AdminMiddleware)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::post('/registration-code/regenerate', [AdminController::class, 'regenerateRegistrationCode'])->name('registration-code.regenerate');
    Route::post('/registration-code/update', [AdminController::class, 'updateRegistrationCode'])->name('registration-code.update');
    Route::put('/users/{user}/password', [AdminController::class, 'updateUserPassword'])->name('users.password.update');
    Route::post('/users/{user}/send-reset-link', [AdminController::class, 'sendUserPasswordResetLink'])->name('users.send-reset-link');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
});

require __DIR__.'/auth.php';
