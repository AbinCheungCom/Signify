<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): \Illuminate\Contracts\View\View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        // 邮箱统一转为小写存储
        $request->merge(['email' => Str::lower($request->email)]);

        $request->validate([
            'email' => 'required|string|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::min(6)],
        ]);

        $user = User::create([
            // 注册不填姓名：以邮箱前缀作为默认展示名（档案姓名在个人中心单独设置）
            'name' => Str::before($request->email, '@'),
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        // 触发验证邮件（User 已实现 MustVerifyEmail）。
        // SMTP 未配置/发送失败不阻断注册，用户可在「验证邮箱」页手动重发。
        try {
            event(new Registered($user));
        } catch (\Throwable $e) {
            \Log::warning('注册验证邮件发送失败：'.$e->getMessage());
        }

        // 未验证邮箱时引导到验证页（创建企业家档案需先验证邮箱）
        return redirect()->intended(
            $user->hasVerifiedEmail() ? route('home') : route('verification.notice')
        );
    }
}