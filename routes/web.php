<?php

use App\Http\Controllers\EntrepreneurController;
use App\Http\Controllers\MyProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 公开路由
|--------------------------------------------------------------------------
*/
Route::get('/', [EntrepreneurController::class, 'home'])->name('home');
Route::get('/entrepreneurs', [EntrepreneurController::class, 'index'])->name('entrepreneurs.index');
Route::get('/entrepreneurs/{entrepreneur}', [EntrepreneurController::class, 'show'])->name('entrepreneurs.show');

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

require __DIR__.'/auth.php';
