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
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\ShareBonusController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Language switching
Route::post('/language/switch', [LanguageController::class, 'switch'])->name('language.switch');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Removed the following test route:
// Route::get('/test-member-edit', function () {
//     $member = \App\Models\Member::find(1);
//     return view('members.edit', compact('member'));
// });



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/calendar', [AdminController::class, 'calendar'])->name('calendar');
    
    // Form Components Demo
    Route::match(['get', 'post'], '/form-demo', [AdminController::class, 'formDemo'])->name('form-demo');
    Route::get('/form-documentation', [AdminController::class, 'formDocumentation'])->name('form-documentation');
     Route::get('/sweet-alert-demo', [AdminController::class, 'sweetAlertDemo'])->name('sweet-alert-demo');
     Route::get('/sweet-alert-documentation', [AdminController::class, 'sweetAlertDocumentation'])->name('sweet-alert-documentation');
    
    // Reports
Route::get('/reports', [ReportsController::class, 'index'])->name('reports');
Route::get('/reports/members/excel', [ReportsController::class, 'exportMembersExcel'])->name('reports.members.excel');
Route::get('/reports/members/pdf', [ReportsController::class, 'exportMembersPdf'])->name('reports.members.pdf');
Route::get('/reports/loans/excel', [ReportsController::class, 'exportLoansExcel'])->name('reports.loans.excel');
Route::get('/reports/loans/pdf', [ReportsController::class, 'exportLoansPdf'])->name('reports.loans.pdf');
Route::get('/reports/savings/excel', [ReportsController::class, 'exportSavingsExcel'])->name('reports.savings.excel');
Route::get('/reports/savings/pdf', [ReportsController::class, 'exportSavingsPdf'])->name('reports.savings.pdf');
Route::get('/reports/transactions/excel', [ReportsController::class, 'exportTransactionsExcel'])->name('reports.transactions.excel');
Route::get('/reports/transactions/pdf', [ReportsController::class, 'exportTransactionsPdf'])->name('reports.transactions.pdf');
Route::get('/reports/branches/excel', [ReportsController::class, 'exportBranchesExcel'])->name('reports.branches.excel');
Route::get('/reports/branches/pdf', [ReportsController::class, 'exportBranchesPdf'])->name('reports.branches.pdf');
Route::get('/reports/summary/excel', [ReportsController::class, 'exportSummaryExcel'])->name('reports.summary.excel');
Route::get('/reports/summary/pdf', [ReportsController::class, 'exportSummaryPdf'])->name('reports.summary.pdf');

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
    Route::get('/members/generate-number/{branchId}', [AdminMemberController::class, 'generateNumber'])->name('members.generate-number');
    Route::delete('/members/{member}/kyc-document/{index}', [AdminMemberController::class, 'deleteKycDocument'])->name('admin.members.delete-kyc-document');
    Route::get('/members/{member}/export/excel', [AdminMemberController::class, 'exportExcel'])->name('members.export.excel');
    Route::get('/members/{member}/export/pdf', [AdminMemberController::class, 'exportPdf'])->name('members.export.pdf');
    
    // Loans Management
    Route::resource('loans', AdminLoanController::class);
    Route::post('/loans/{loan}/approve', [AdminLoanController::class, 'approve'])->name('loans.approve');
    Route::post('/loans/{loan}/disburse', [AdminLoanController::class, 'disburse'])->name('loans.disburse');
    Route::post('/loans/{loan}/reject', [AdminLoanController::class, 'reject'])->name('loans.reject');
    
    // Penalty Management
    Route::post('/loans/calculate-penalties', [AdminLoanController::class, 'calculatePenalties'])->name('loans.calculate-penalties');
    Route::post('/loans/{loan}/waive-penalties', [AdminLoanController::class, 'waiveLoanPenalties'])->name('loans.waive-penalties');
    Route::post('/installments/{installment}/waive-penalty', [AdminLoanController::class, 'waivePenalty'])->name('installments.waive-penalty');
    Route::get('/loans/penalty-statistics', [AdminLoanController::class, 'penaltyStatistics'])->name('loans.penalty-statistics');
    
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
        Route::get('/documentation', [ReportController::class, 'documentation'])->name('documentation');
    });
    
    // Finance Statements with Share Bonus Calculations
    Route::prefix('finance-statements')->name('finance-statements.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\FinanceStatementController::class, 'index'])->name('index');
        Route::post('/generate', [\App\Http\Controllers\Admin\FinanceStatementController::class, 'generate'])->name('generate');
        Route::get('/export-pdf', [\App\Http\Controllers\Admin\FinanceStatementController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export-excel', [\App\Http\Controllers\Admin\FinanceStatementController::class, 'exportExcel'])->name('export-excel');
    });
    
    // Share Bonus Generation and Management
    Route::prefix('share-bonus')->name('share-bonus.')->group(function () {
        Route::get('/', [ShareBonusController::class, 'index'])->name('index');
        Route::post('/generate', [ShareBonusController::class, 'generate'])->name('generate');
        Route::get('/generate', function () {
            return redirect()->route('admin.share-bonus.index')
                ->with('error', 'Please use the form below to generate a share bonus statement.');
        });
        Route::post('/apply', [ShareBonusController::class, 'apply'])->name('apply');
        Route::post('/undo', [ShareBonusController::class, 'undo'])->name('undo');
        Route::get('/{id}/show', [ShareBonusController::class, 'show'])->name('show');
        Route::get('/{id}/print', [ShareBonusController::class, 'print'])->name('print');
        Route::get('/{id}/export-pdf', [ShareBonusController::class, 'exportPDF'])->name('export-pdf');
        Route::get('/{id}/export-excel', [ShareBonusController::class, 'exportExcel'])->name('export-excel');
        Route::delete('/{id}', [ShareBonusController::class, 'destroy'])->name('destroy');
    });
    

});

require __DIR__.'/auth.php';
