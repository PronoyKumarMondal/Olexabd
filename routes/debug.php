<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Add this temporarily to routes/web.php or a new debug file

Route::get('/debug-tracking', function (Request $request) {
    return response()->json([
        'session_id' => session()->getId(),
        'order_source' => session('order_source', 'NOT_SET'),
        'all_session' => session()->all(),
        'host' => $request->getHost(),
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);
});
