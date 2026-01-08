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
        
        $permissions = [
            'storage' => is_writable(storage_path()),
            'storage/app' => is_writable(storage_path('app')),
            'storage/framework' => is_writable(storage_path('framework')),
            'storage/logs' => is_writable(storage_path('logs')),
            'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
        ];

        // 6. Advanced Monitoring (Tech Head)
        // Git Info
        $gitInfo = 'Version info unavailable';
        try {
            if (function_exists('exec')) {
                $gitHash = trim(exec('git log -1 --format="%h"'));
                $gitMessage = trim(exec('git log -1 --format="%s"'));
                $gitDate = trim(exec('git log -1 --format="%ci"'));
                if ($gitHash) {
                    $gitInfo = "[$gitHash] $gitMessage ($gitDate)";
                }
            }
        } catch (\Throwable $e) {
            $gitInfo = 'Git Check Failed';
        }

        // Database Tables Stats (Top 5 by Size/Rows) - MySQL Specific
        $tableStats = [];
        try {
            $dbName = DB::connection()->getDatabaseName();
            // This query gets row counts and size
            $tableStats = DB::select("
                SELECT table_name, table_rows, 
                ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb 
                FROM information_schema.TABLES 
                WHERE table_schema = ? 
                ORDER BY size_mb DESC, table_rows DESC 
                LIMIT 5
            ", [$dbName]);
        } catch (\Throwable $e) {
             // Fallback: Create a dummy object or empty array
        }

        // Failed Jobs
        $failedJobsCount = 0;
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('failed_jobs')) {
                $failedJobsCount = DB::table('failed_jobs')->count();
            }
        } catch (\Throwable $e) {}

        // Route Count
        $routeCount = 0;
        try {
            $routeCount = count(\Illuminate\Support\Facades\Route::getRoutes());
        } catch (\Throwable $e) {}
        
        // Maintenance Mode
        $maintenanceMode = app()->isDownForMaintenance();

        // Active Sessions (approximate if using database driver, else N/A)
        $activeSessions = 'N/A';
        try {
            if (config('session.driver') === 'database' && \Illuminate\Support\Facades\Schema::hasTable('sessions')) {
                // Active in last hour
                $activeSessions = DB::table('sessions')
                    ->where('last_activity', '>=', now()->subHour()->timestamp)
                    ->count();
            }
        } catch (\Throwable $e) {}

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
            'recentLogs',
            'permissions', 'gitInfo', 'tableStats', 
            'failedJobsCount', 'routeCount', 'maintenanceMode', 'activeSessions'
        ));
    }
}
