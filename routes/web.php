<?php

use App\Http\Controllers\EntrepreneurController;
use App\Http\Controllers\MyProfileController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 公开路由
|--------------------------------------------------------------------------
*/
Route::get('/', [EntrepreneurController::class, 'home'])->name('home');
Route::get('/entrepreneurs', [EntrepreneurController::class, 'index'])->name('entrepreneurs.index');
Route::get('/entrepreneurs/{id}', [EntrepreneurController::class, 'show'])->name('entrepreneurs.show')->whereNumber('id');

/*
|--------------------------------------------------------------------------
| 需要登录的路由
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // 个人中心
    Route::get('/my/profile', [MyProfileController::class, 'show'])->name('profile.show');
    Route::patch('/my/profile', [MyProfileController::class, 'update'])->name('profile.update');
    Route::post('/my/profile', [MyProfileController::class, 'create'])->name('profile.create');
});

/*
|--------------------------------------------------------------------------
| 管理员路由（中间件 + Policy 双重保护）
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/entrepreneurs', [AdminController::class, 'entrepreneurs'])->name('entrepreneurs');
    Route::post('/entrepreneurs/batch-approve', [AdminController::class, 'batchApprove'])->name('batch-approve')->middleware('throttle:30,1');
    Route::post('/entrepreneurs/batch-reject', [AdminController::class, 'batchReject'])->name('batch-reject')->middleware('throttle:30,1');
    Route::post('/entrepreneurs/{entrepreneur}/approve', [AdminController::class, 'approve'])->name('approve');
    Route::post('/entrepreneurs/{entrepreneur}/reject', [AdminController::class, 'reject'])->name('reject');
    Route::post('/entrepreneurs/{entrepreneur}/toggle-featured', [AdminController::class, 'toggleFeatured'])->name('toggle-featured');
    Route::delete('/entrepreneurs/{entrepreneur}', [AdminController::class, 'destroy'])->name('destroy');
});

require __DIR__.'/auth.php';
