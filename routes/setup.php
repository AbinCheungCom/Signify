<?php

use App\Http\Controllers\Setup\SetupController;
use Illuminate\Support\Facades\Route;

// 安装向导（需要在 auth 中间件之外）
// 注意：这三个是无认证端点，安全性依赖 isInstalled() 锁定 + 限流；
// 安装成功后写入 storage/app/installed.lock，接口永久拒绝重复安装。
Route::get('/setup', [SetupController::class, 'check'])->name('setup');
Route::post('/setup/test-db', [SetupController::class, 'testDb'])
    ->name('setup.test-db')
    ->middleware('throttle:10,1');
Route::post('/setup/install', [SetupController::class, 'install'])
    ->name('setup.install')
    ->middleware('throttle:5,1');
