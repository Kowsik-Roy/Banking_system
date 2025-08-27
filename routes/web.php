<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Admin routes - temporarily without admin middleware
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/admin/deposit/{userId}', [AdminController::class, 'showDepositForm'])->name('admin.deposit.form');
    Route::post('/admin/deposit/{userId}', [AdminController::class, 'deposit'])->name('admin.deposit');
    Route::get('/admin/withdraw/{userId}', [AdminController::class, 'showWithdrawForm'])->name('admin.withdraw.form');
    Route::post('/admin/withdraw/{userId}', [AdminController::class, 'withdraw'])->name('admin.withdraw');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/pin', [ProfileController::class, 'updatePin'])->name('profile.update-pin');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth','verified'])->group(function () {
    Route::get('/transfer', [TransferController::class, 'showForm'])->name('transfer.create');
    Route::post('/transfer', [TransferController::class, 'transfer'])->name('transfer.store');
    Route::post('/transfer/init', [TransferController::class, 'init'])->name('transfer.init');
    
    // Transaction History Routes
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/download', [TransactionController::class, 'download'])->name('transactions.download');
    Route::get('/transactions/{id}', [TransactionController::class, 'show'])->name('transactions.show');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/set-pin', [UserController::class, 'showPinForm'])->name('user.setPin');
    Route::post('/save-pin', [UserController::class, 'savePin'])->name('user.savePin');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/verify-email', [EmailVerificationController::class, 'showForm'])->name('verify.email.form');
    Route::post('/verify-email', [EmailVerificationController::class, 'verifycode'])->name('verify.email.code');
});

require __DIR__.'/auth.php';