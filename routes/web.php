<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Models\User;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});




Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');



Route::resource('users', UserController::class);

Route::get('manage-users', [UserController::class, 'index'])->name('manage.users');
// AJAX user search for admin
Route::get('/admin/user-search', [UserController::class, 'ajaxUserSearch'])->name('admin.user.search');
Route::get('manage-media', function () {
    return view('admin.manage-media');
})->name('manage.media');
Route::get('ai-feedback', function () {
    return view('admin.ai-feedback');
})->name('ai.feedback');

Route::get('ai-usage', function () {
    return view('admin.ai-usage');
})->name('ai.usage');
Route::get('analytics', function () {
    return view('admin.analytics');
})->name('analytics');





Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::get('/verify', [AuthController::class, 'showVerifyForm'])->name('verify.email');
Route::post('/verify', [AuthController::class, 'verifyOtp'])->name('verify.email.submit');


Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [AuthController::class, 'editProfile'])->name('profile.edit');
    Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
    
});
 
  Route::post('change-role/{user}', [AuthController::class, 'updateRole'])->name('users.updateRole');

