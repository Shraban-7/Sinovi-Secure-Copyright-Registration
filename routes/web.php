<?php

use App\Models\NidVerification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\NidVerificationController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    $user = Auth::user();
    $extractedNidNumber = session('extractedNidNumber');
    $all_nid_verifications = NidVerification::all();
     $user_nids = NidVerification::where('email', $user->email)->get();
    return view('dashboard', compact( 'all_nid_verifications','user_nids'));
})->middleware(['auth', 'verified','twofactor'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/nid-verification', [NidVerificationController::class, 'verify'])->name('nid.verify');
});

Route::middleware(['auth', 'twofactor'])->group(function () {
    Route::get('verify/resend', [TwoFactorController::class, 'resend'])->name('verify.resend');
    Route::resource('verify', TwoFactorController::class)->only(['index', 'store']);
});


require __DIR__ . '/auth.php';
