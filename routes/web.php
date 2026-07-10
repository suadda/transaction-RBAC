<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', fn () => view('welcome'));

Route::get('/dashboard', function () {
    return redirect()->route(match (optional(Auth::user()->role)->name) {
        'Staff'                => 'staff.submissions.index',
        'SPV', 'Manager', 'Direktur' => 'approval.index',
        'Finance'              => 'finance.index',
        default                => 'profile.edit',
    });
})->middleware(['auth', 'verified'])->name('dashboard');

// --- STAFF ---
Route::middleware(['auth', 'role:Staff'])->group(function () {
    Route::get('/staff/dashboard', [SubmissionController::class, 'create'])->name('staff.dashboard');
    Route::get('/staff/submissions', [SubmissionController::class, 'index'])->name('staff.submissions.index');
    Route::post('/staff/submissions', [SubmissionController::class, 'store'])->name('staff.submissions.store');
    Route::get('/staff/submissions/{submission}', [SubmissionController::class, 'show'])->name('staff.submissions.show');
});

// --- APPROVAL (SPV / Manager / Direktur) ---
Route::middleware(['auth', 'role:SPV,Manager,Direktur'])->group(function () {
    Route::get('/approval', [ApprovalController::class, 'index'])->name('approval.index');
    Route::get('/approval/{submission}', [ApprovalController::class, 'show'])->name('approval.show');
    Route::post('/approval/{submission}/approve', [ApprovalController::class, 'approve'])->name('approval.approve');
    Route::post('/approval/{submission}/reject', [ApprovalController::class, 'reject'])->name('approval.reject');
});

// --- FINANCE ---
Route::middleware(['auth', 'role:Finance'])->group(function () {
    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::post('/finance/{submission}/pay', [FinanceController::class, 'pay'])->name('finance.pay');
});

// --- PROFIL BAWAAN BREEZE ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';