<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    public function index()
    {
        // List only Admins (admins table)
        // Hide the current admin to prevent self-deletion issues if we add that later
        $users = Admin::where('id', '!=', auth('admin')->id())->get();
        return view('admin.super.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins', // Check admins table
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,super_admin',
            'permissions' => 'array',
        ]);

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true, // Ensure default active
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->back()->with('success', "New Admin created successfully.");
    }

    public function updateRole(Request $request, Admin $user) // Type hint Admin
    {
        $request->validate([
            'role' => 'required|in:admin,super_admin', // Removed customer option
            'permissions' => 'array',
        ]);

        $user->update([
            'role' => $request->role,
            'permissions' => $request->permissions ?? [] 
        ]);
        
        return redirect()->back()->with('success', "Admin role and permissions updated.");
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
