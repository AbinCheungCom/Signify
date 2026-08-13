<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): \Illuminate\Http\RedirectResponse|\Illuminate\Contracts\View\View
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('home'))
                    : view('auth.verify-email');
    }
}