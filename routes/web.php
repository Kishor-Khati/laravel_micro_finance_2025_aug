<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BranchController as AdminBranchController;
use App\Http\Controllers\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\Admin\LoanController as AdminLoanController;
use App\Http\Controllers\Admin\SavingsController as AdminSavingsController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Language switching
Route::post('/language/switch', [LanguageController::class, 'switch'])->name('language.switch');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Users Management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.destroy');
    
    // Branches Management
    Route::resource('branches', AdminBranchController::class);
    
    // Members Management
    Route::resource('members', AdminMemberController::class);
    
    // Loans Management
    Route::resource('loans', AdminLoanController::class);
    
    // Savings Management
    Route::resource('savings', AdminSavingsController::class);
    Route::post('/savings/{savingsAccount}/deposit', [AdminSavingsController::class, 'deposit'])->name('savings.deposit');
    Route::post('/savings/{savingsAccount}/withdraw', [AdminSavingsController::class, 'withdraw'])->name('savings.withdraw');
    
    // Transactions Management
    Route::resource('transactions', TransactionController::class)->only(['index', 'show', 'create', 'store']);
    
    // Expense Management
    Route::resource('expenses', ExpenseController::class);
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
        Route::get('/members', [ReportController::class, 'members'])->name('members');
        Route::get('/loans', [ReportController::class, 'loans'])->name('loans');
        Route::get('/branches', [ReportController::class, 'branches'])->name('branches');
        Route::get('/transactions', [ReportController::class, 'transactions'])->name('transactions');
        Route::get('/summary', [ReportController::class, 'summary'])->name('summary');
    });
});

require __DIR__.'/auth.php';
