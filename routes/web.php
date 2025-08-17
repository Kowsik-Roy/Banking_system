<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\TransferController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth','verified'])->group(function () {
    Route::get('/transfer', [TransferController::class, 'showForm'])->name('transfer.create');
    Route::post('/transfer', [TransferController::class, 'transfer'])->name('transfer.store');
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