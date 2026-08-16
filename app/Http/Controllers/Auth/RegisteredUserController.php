<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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

        return redirect()->intended(route('home'));
    }
}