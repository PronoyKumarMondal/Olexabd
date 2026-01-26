<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    // 1. Redirect to Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // 2. Handle Callback
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Google Login Failed: ' . $e->getMessage());
        }

        // Check if user exists
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // Existing user, update google_id if missing
            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }
        } else {
            // Create New User
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password' => Hash::make(Str::random(24)), // Random password
                'phone' => null, // Needs input
                'address' => null, // Optional initially, but can be asked later
                 // If role/source needed, default to customer/google
                'role' => 'customer',
                'source' => 'google'
            ]);
        }

        Auth::login($user);

        // Check Phone
        if (empty($user->phone)) {
            return redirect()->route('auth.phone.form');
        }

        return redirect()->intended(route('shop.index'));
    }

    // 3. Show Phone Form
    public function showPhoneForm()
    {
        return view('auth.phone_verification');
    }

    // 4. Save Phone
    public function savePhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/(01)[0-9]{9}/',
            'address' => 'required|string|max:500' // While we are at it, get address too
        ]);

        $user = Auth::user();
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->save();

        return redirect()->route('shop.index')->with('success', 'Registration Completed!');
    }
}
