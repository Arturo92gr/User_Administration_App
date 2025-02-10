<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdministratorsController;

/* Route::get('/', function () {
    return view('welcome');
});
 */

// 1º
Auth::routes(['verify' => true]);

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('index');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'home'])->name('home');
Route::get('/verificado', [App\Http\Controllers\HomeController::class, 'verificado'])->name('verificado');

// Profile
Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/profile/manage', [ProfileController::class, 'manage'])->name('profile.manage');
Route::get('/profile/password', [ProfileController::class, 'show'])->name('profile.password');
Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

// Redirección según el rol
Route::get('/redirect', [App\Http\Controllers\HomeController::class, 'redirectBasedOnRole'])->name('redirect');

// Administrators
Route::middleware(['auth', 'App\Http\Middleware\AdminMiddleware'])->group(function () {
    Route::get('/admin', [AdministratorsController::class, 'index'])->name('admin.index');
    Route::get('/admin/edit/{id}', [AdministratorsController::class, 'editProfile'])->name('admin.edit');
    Route::post('/admin/update/{id}', [AdministratorsController::class, 'updateProfile'])->name('admin.update');
    Route::post('/admin/verify-email/{id}', [AdministratorsController::class, 'verifyEmail'])->name('admin.verifyEmail');
    Route::post('/admin/assign-admin/{id}', [AdministratorsController::class, 'assignAdminRole'])->name('admin.assignAdmin');
    Route::delete('/admin/destroy/{id}', [AdministratorsController::class, 'destroy'])->name('admin.destroy');
});

// SuperAdmin
Route::middleware(['auth', 'App\Http\Middleware\SuperAdminMiddleware'])->group(function () {
    Route::get('/superadmin', [AdministratorsController::class, 'indexSuper'])->name('superadmin.index');
    Route::get('/superadmin/edit/{id}', [AdministratorsController::class, 'editAdmin'])->name('superadmin.edit');
    Route::post('/superadmin/update/{id}', [AdministratorsController::class, 'updateAdmin'])->name('superadmin.update');
    Route::post('/superadmin/verify-email/{id}', [AdministratorsController::class, 'verifyEmail'])->name('superadmin.verifyEmail');
    Route::post('/superadmin/assign-superadmin/{id}', [AdministratorsController::class, 'assignSuperAdminRole'])->name('superadmin.assignSuperAdmin');
    Route::delete('/superadmin/destroy/{id}', [AdministratorsController::class, 'destroy'])->name('superadmin.destroy');
});