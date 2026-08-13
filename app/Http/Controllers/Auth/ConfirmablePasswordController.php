<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ConfirmablePasswordController extends Controller
{
    /**
     * Display the password confirmation view.
     */
    public function show(): \Illuminate\Contracts\View\View
    {
        return view('auth.confirm-password');
    }

    /**
     * Confirm the user's password.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            return back()->withErrors(['password' => '密码不正确']);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('home'));
    }
}