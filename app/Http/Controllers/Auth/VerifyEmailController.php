<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;

use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email address as verified (Stateless).
     */
    public function __invoke(\Illuminate\Http\Request $request, $id, $hash): RedirectResponse
    {
        $user = \App\Models\User::find($id);

        if (! $user) {
            return redirect()->route('login')->with('error', 'Invalid verification link.');
        }

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')->with('error', 'Invalid or expired verification link.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('shop.index')->with('success', 'Email is already verified.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->route('shop.index')->with('success', 'Email Verified Successfully! Please Login if needed.');
    }
}
