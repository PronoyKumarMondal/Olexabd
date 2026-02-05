<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    //
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // Check if already exists
        if (\App\Models\NewsletterSubscriber::where('email', $request->email)->exists()) {
            return back()->with('success', 'You are already subscribed!');
        }

        \App\Models\NewsletterSubscriber::create([
            'email' => $request->email,
            'is_active' => true
        ]);

        return back()->with('success', 'Thank you for subscribing to our newsletter!');
    }
}
