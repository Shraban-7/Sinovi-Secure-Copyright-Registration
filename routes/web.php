<?php

use App\Http\Controllers\NidVerificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TwoFactorController;
use App\Models\NidVerification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    $user = Auth::user();
    $extractedNidNumber = session('extractedNidNumber');
    $all_nid_verifications = NidVerification::all();
    $user_nids = NidVerification::where('email', $user->email)->get();
    $users = User::where('is_admin',0)->get();

    return view('dashboard', compact('all_nid_verifications', 'user_nids','users'));
})->middleware(['auth', 'verified'])->name('dashboard');

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
