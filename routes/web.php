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

// --- GRUP RUTE STAFF ---
Route::middleware(['auth', 'role:Staff'])->group(function () {
    // 1. Rute GET untuk menampilkan halaman form
    Route::get('/staff/dashboard', [SubmissionController::class, 'create'])->name('staff.dashboard');
    
    // 2. Rute POST untuk mengirim data ke database
    Route::post('/staff/submissions', [SubmissionController::class, 'store'])->name('staff.submissions.store');
});

// --- GRUP RUTE APPROVAL (Bos) ---
Route::middleware(['auth', 'role:Manager,Direktur'])->group(function () {
    Route::get('/approval/dashboard', function () {
        return "Halaman Approval Bos";
    })->name('approval.dashboard');
});

// --- RUTE PROFIL BAWAAN BREEZE ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';