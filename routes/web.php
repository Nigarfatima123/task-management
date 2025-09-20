<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

// Admin Auth
use App\Http\Controllers\AdminAuthController;
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// User Auth
use App\Http\Controllers\UserAuthController;
Route::get('/user/login', [UserAuthController::class, 'showLogin'])->name('user.login');
Route::post('/user/login', [UserAuthController::class, 'login'])->name('user.login.submit');
Route::post('/user/logout', [UserAuthController::class, 'logout'])->name('user.logout');

// Admin Dashboard (to be implemented)
Route::get('/admin/dashboard', function() {
	// Only allow if admin is logged in
	if (!session('admin_id')) return redirect()->route('admin.login');
	return view('admin.dashboard');
})->name('admin.dashboard');

// User Dashboard (to be implemented)
Route::get('/user/dashboard', function() {
	// Only allow if user is logged in
	if (!session('user_id')) return redirect()->route('user.login');
	return view('user.dashboard');
})->name('user.dashboard');

// Existing task routes (for admin panel)
Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
Route::post('/tasks/{task}/toggle', [TaskController::class, 'toggleComplete'])->name('tasks.toggle');
Route::post('/tasks/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');
