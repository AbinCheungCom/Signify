<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): \Inertia\Response
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('home'))
                    : inertia('Auth/VerifyEmail');
    }
}