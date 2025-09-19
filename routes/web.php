<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Models\User;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ComplaintTrackController;

Route::get('/', function () {
    return view('welcome');
});




Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');



Route::resource('users', UserController::class);

Route::get('manage-users', [UserController::class, 'index'])->name('manage.users');
// AJAX user search for admin
// Route::get('/admin/user-search', [UserController::class, 'ajaxUserSearch'])->name('admin.user.search');
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



// Users
Route::get('/admin/user-search', [UserController::class, 'ajaxUserSearch'])->name('admin.user.search');


Route::get('/admin/user-list', [UserController::class, 'ajaxUserList'])->name('admin.user.list');

// Media
Route::get('/admin/media-search', [UserController::class, 'ajaxMediaSearch'])->name('admin.media.search');

// AI
Route::get('/admin/ai-search', [UserController::class, 'ajaxAISearch'])->name('admin.ai.search');

// Analytics
Route::get('/admin/analytics-search', [UserController::class, 'ajaxAnalyticsSearch'])->name('admin.analytics.search');

//complaints
Route::get('/admin/complaints/ajax-search', [AdminComplaintController::class, 'ajaxSearch'])->name('admin.complaints.ajaxSearch');



Route::prefix('admin')->name('admin.')->group(function () {

    // List all complaints with filters
    Route::get('complaints', [AdminComplaintController::class, 'index'])->name('complaints.index');

    // View a single complaint
    Route::get('complaints/{id}', [AdminComplaintController::class, 'show'])->name('complaints.show');

    // Assign complaint to an officer
    Route::post('complaints/{id}/assign', [AdminComplaintController::class, 'assign'])->name('complaints.assign');

    // Change complaint status
    Route::post('complaints/{id}/change-status', [AdminComplaintController::class, 'changeStatus'])->name('complaints.changeStatus');

    // Delete complaint
    Route::delete('/complaints/{id}', [AdminComplaintController::class, 'destroy'])->name('complaints.destroy');
    // ✅ Update complaint (status, officer, notes)
    Route::put('complaints/{id}', [AdminComplaintController::class, 'update'])->name('complaints.update');
});




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







  //Public User Routes
  Route::get('/Public/welcome', function () {
    return view('Public User.welcome');
  })->name('public.welcome');
    Route::get('/Public/dashboard', function () {
        return view('public_user.dashboard');
    })->name('public.dashboard');
//     Route::get('/Public/complaints-form', function () {
//     return view('public_user.complaints-form');
// })->name('public.complaints.form');
Route::get('/Public/complaints-form', [ComplaintController::class, 'store'])->name('public.complaints.form');
  Route::get('/Public/complaints-form', [ComplaintController::class, 'create'])->name('public.complaints.form');
  Route::get('/Public/complaints-track', [ComplaintTrackController::class, 'index'])->name('public.complaints.track');

    Route::get('/Public/public-alerts', function () {
        return view('public_user.public-alerts');
    })->name('public.alerts');
    Route::get('/Public/profile-update', function () {
        return view('public_user.profile-update');
    })->name('public.profile.update');



    Route::post('/complaints/store', [ComplaintController::class, 'store'])->name('complaints.store');


Route::get('/complaints-track', [ComplaintTrackController::class, 'index'])->name('complaints.track');
Route::post('/complaints-track', [ComplaintTrackController::class, 'track'])->name('complaints.track.submit');


// routes/web.php
Route::get('/complaints/form', [ComplaintController::class, 'create'])->name('public.complaints.form');
