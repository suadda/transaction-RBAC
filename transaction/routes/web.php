<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:Staff'])->group(function () {
    Route::get('/staff/dashboard', [SubmissionController::class, 'create'])->name('staff.dashboard');
});

Route::middleware(['auth', 'role:Manager,Direktur'])->group(function () {
    Route::get('/approval/dashboard', function () {
        return "Halaman Approval Bos";
    })->name('approval.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
