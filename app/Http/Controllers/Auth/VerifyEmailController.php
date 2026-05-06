<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        if ($request->user()->markEmailAsVerified()) {
            return redirect()->intended(route('home').'?verified=1');
        }

        return redirect()->back();
    }
}