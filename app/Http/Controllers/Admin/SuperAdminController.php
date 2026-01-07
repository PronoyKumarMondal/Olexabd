<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    public function index()
    {
        // List only Admins and Super Admins (hide customers)
        $users = User::where('id', '!=', auth()->id())
                     ->whereIn('role', ['admin', 'super_admin'])
                     ->get();
        return view('admin.super.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,super_admin',
            'permissions' => 'array',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => $request->role,
            'is_admin' => true,
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->back()->with('success', "New Admin created successfully.");
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:customer,admin,super_admin',
            'permissions' => 'array', // Permissions array
        ]);

        $user->update([
            'role' => $request->role,
            'permissions' => $request->permissions ?? [] // Save permissions as JSON
        ]);
        
        // Sync backwards compatibility if needed
        $user->is_admin = ($request->role === 'admin' || $request->role === 'super_admin');
        $user->save();

        return redirect()->back()->with('success', "User role and permissions updated.");
    }

    public function health()
    {
        // 1. Database Extended Info
        try {
            $pdo = DB::connection()->getPdo();
            $dbStatus = 'Connected';
            $dbVersion = $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);
            $dbDriver = DB::connection()->getDriverName();
        } catch (\Exception $e) {
            $dbStatus = 'Disconnected: ' . $e->getMessage();
            $dbVersion = 'N/A';
            $dbDriver = 'N/A';
        }

        // 2. Server Environment
        $serverInfo = [
            'os' => php_uname('s') . ' ' . php_uname('r'),
            'php_version' => phpversion(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'max_execution_time' => ini_get('max_execution_time') . 's',
            'memory_limit' => ini_get('memory_limit'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
        ];

        // 3. Application Config & Security
        $appInfo = [
            'environment' => config('app.env'),
            'debug_mode' => config('app.debug'),
            'url' => config('app.url'),
            'timezone' => config('app.timezone'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_connection' => config('queue.default'),
        ];

        // 4. Critical Extensions Check
        $extensions = [
            'openssl' => extension_loaded('openssl'),
            'pdo' => extension_loaded('pdo'),
            'mbstring' => extension_loaded('mbstring'),
            'tokenizer' => extension_loaded('tokenizer'),
            'xml' => extension_loaded('xml'),
            'ctype' => extension_loaded('ctype'),
            'json' => extension_loaded('json'),
        ];

        // 5. Storage Check
        $storagePath = storage_path();
        $storageWritable = is_writable($storagePath);
        $diskFree = function_exists('disk_free_space') ? round(disk_free_space($storagePath) / 1024 / 1024 / 1024, 2) . ' GB' : 'N/A';
        
        // Log File Size & Content
        $logFile = storage_path('logs/laravel.log');
        $logSize = file_exists($logFile) ? round(filesize($logFile) / 1024 / 1024, 2) . ' MB' : 'No Log File';
        
        $recentLogs = [];
        if (file_exists($logFile)) {
            $logContent = file($logFile);
            $recentLogs = array_slice($logContent, -20); // Last 20 lines
            $recentLogs = array_reverse($recentLogs); // Newest first
        }

        return view('admin.super.health', compact(
            'dbStatus', 'dbVersion', 'dbDriver',
            'serverInfo', 'appInfo', 'extensions',
            'storageWritable', 'diskFree', 'logSize',
            'recentLogs'
        ));
    }
}
