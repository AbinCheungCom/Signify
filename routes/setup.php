<?php

use App\Http\Controllers\Setup\SetupController;
use Illuminate\Support\Facades\Route;

// 安装向导（需要在 auth 中间件之外）
Route::get('/setup', [SetupController::class, 'check'])->name('setup');
Route::post('/setup/test-db', [SetupController::class, 'testDb'])->name('setup.test-db');
Route::post('/setup/install', [SetupController::class, 'install'])->name('setup.install');