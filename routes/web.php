<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalaryAllocationController;
use App\Http\Controllers\SavingGoalController;
use App\Http\Controllers\TransactionHistoryController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pembagian Gaji & Kesimpulan Saldo
    Route::get('/salary-allocations', [SalaryAllocationController::class, 'index'])->name('salary-allocations.index');
    Route::post('/salary-allocations', [SalaryAllocationController::class, 'store'])->name('salary-allocations.store');
    Route::patch('/salary-allocations/{salary_allocation}/toggle', [SalaryAllocationController::class, 'toggle'])->name('salary-allocations.toggle');
    Route::post('/salary-allocations/balance', [SalaryAllocationController::class, 'updateBalance'])->name('salary-allocations.balance');
    Route::delete('/salary-allocations/{salary_allocation}', [SalaryAllocationController::class, 'destroy'])->name('salary-allocations.destroy');

    // Keuangan: Pemasukan, Pengeluaran, & Riwayat
    Route::resource('income', IncomeController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('expenses', ExpenseController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('/transactions', [TransactionHistoryController::class, 'index'])->name('transactions.index');
    Route::delete('/transactions/{transaction}', [TransactionHistoryController::class, 'destroy'])->name('transactions.destroy');

    // Perencanaan: Budgeting & Saving Goals
    Route::resource('budgets', BudgetController::class)->only(['index', 'store', 'destroy']);
    Route::resource('saving-goals', SavingGoalController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('/saving-goals/{saving_goal}/mutate', [SavingGoalController::class, 'mutate'])->name('saving-goals.mutate');

    // Laporan
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Master Data
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('payment-methods', PaymentMethodController::class)->only(['index', 'store', 'update', 'destroy']);

    // Profile & Password
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});