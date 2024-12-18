<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

/* Route::get('/', function () {
    return view('welcome');
});
 */

// 1º
Auth::routes(['verify' => true]);

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('index');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'home'])->name('home');
Route::get('/verificado', [App\Http\Controllers\HomeController::class, 'verificado'])->name('verificado');

// Administrators routes
Route::get('admin', [App\Http\Controllers\AdministratorsController::class, 'index'])->name('admin.index');
Route::get('super', [App\Http\Controllers\AdministratorsController::class, 'indexSuper'])->name('super.index');

// Profile routes
Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/profile/password', [ProfileController::class, 'show'])->name('profile.password');
Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');