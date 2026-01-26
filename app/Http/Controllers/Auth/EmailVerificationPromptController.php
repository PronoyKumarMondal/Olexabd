<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request)
    {
        // Force refresh user from DB to ensure we see the verification
        return $request->user()->fresh()->hasVerifiedEmail()
                    ? redirect()->route('shop.index')
                    : view('auth.verify-email', ['status' => session('status')]);
    }
}
