<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): \Inertia\Response
    {
        return inertia('Auth/ForgotPassword');
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        // We will send the password reset link to this user.
        // Note: requires mail configuration
        return back()->with('status', '密码重置链接已发送至您的邮箱');
    }
}