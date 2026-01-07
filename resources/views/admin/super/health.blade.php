@extends('layouts.admin')

@section('header', 'System Health & Security Analysis')

@section('content')
<div class="row g-4">
    <!-- Critical Status Card -->
    <div class="col-12">
        <div class="card border-0 shadow-sm {{ $appInfo['debug_mode'] && $appInfo['environment'] === 'production' ? 'bg-danger text-white' : 'bg-success text-white' }}">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-1 fw-bold"><i class="bi bi-shield-check"></i> Overall System Status</h4>
                    <p class="mb-0 opacity-75">
                        @if($appInfo['debug_mode'] && $appInfo['environment'] === 'production')
                            CRITICAL WARNING: Debug Mode is ON in Production! This is a security risk.
                        @else
                            System is operational and secure.
                        @endif
                    </p>
                </div>
                <div class="text-end">
                    <h2 class="mb-0 fw-bold">{{ $appInfo['environment'] }}</h2>
                    <small>Environment</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Server Environment -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom border-primary border-3">
                <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-cpu-fill"></i> Server Environment</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0 small">
                    <tbody>
                        <tr>
                            <td>Server Time</td>
                            <td class="text-end fw-bold text-primary">{{ now()->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        <tr><td>OS</td><td class="text-end fw-bold">{{ $serverInfo['os'] }}</td></tr>
                        <tr><td>PHP Version</td><td class="text-end fw-bold">{{ $serverInfo['php_version'] }}</td></tr>
                        <tr><td>Server Software</td><td class="text-end text-break">{{ $serverInfo['server_software'] }}</td></tr>
                        <tr><td>Max Execution Time</td><td class="text-end">{{ $serverInfo['max_execution_time'] }}</td></tr>
                        <tr><td>Memory Limit</td><td class="text-end">{{ $serverInfo['memory_limit'] }}</td></tr>
                        <tr><td>Upload Limit</td><td class="text-end">{{ $serverInfo['upload_max_filesize'] }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Application Config -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom border-info border-3">
                <h6 class="mb-0 fw-bold text-info"><i class="bi bi-gear-wide-connected"></i> App Configuration</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0 small">
                    <tbody>
                        <tr><td>App URL</td><td class="text-end text-truncate" style="max-width: 150px;">{{ $appInfo['url'] }}</td></tr>
                        <tr>
                            <td>Debug Mode</td>
                            <td class="text-end">
                                @if($appInfo['debug_mode']) 
                                    <span class="badge bg-warning text-dark">ENABLED</span> 
                                @else 
                                    <span class="badge bg-success">DISABLED</span> 
                                @endif
                            </td>
                        </tr>
                        <tr><td>Timezone</td><td class="text-end">{{ $appInfo['timezone'] }}</td></tr>
                        <tr><td>Cache Driver</td><td class="text-end">{{ $appInfo['cache_driver'] }}</td></tr>
                        <tr><td>Session Driver</td><td class="text-end">{{ $appInfo['session_driver'] }}</td></tr>
                        <tr><td>Queue Connection</td><td class="text-end">{{ $appInfo['queue_connection'] }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Database Info -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom border-warning border-3">
                <h6 class="mb-0 fw-bold text-warning"><i class="bi bi-database-fill-gear"></i> Database</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0 small">
                    <tbody>
                        <tr>
                            <td>Connection Status</td>
                            <td class="text-end">
                                <span class="badge {{ str_contains($dbStatus, 'Connected') ? 'bg-success' : 'bg-danger' }}">
                                    {{ $dbStatus }}
                                </span>
                            </td>
                        </tr>
                        <tr><td>Driver</td><td class="text-end fw-bold">{{ $dbDriver }}</td></tr>
                        <tr><td>Version</td><td class="text-end">{{ $dbVersion }}</td></tr>
                        <tr>
                            <td>Storage Writable</td>
                            <td class="text-end">
                                @if($storageWritable)
                                    <span class="text-success"><i class="bi bi-check-circle-fill"></i> Yes</span>
                                @else
                                    <span class="text-danger"><i class="bi bi-x-circle-fill"></i> No</span>
                                @endif
                            </td>
                        </tr>
                        <tr><td>Disk Free Space</td><td class="text-end">{{ $diskFree }}</td></tr>
                        <tr><td>Log File Size</td><td class="text-end">{{ $logSize }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PHP Extensions -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-puzzle-fill"></i> Required PHP Extensions</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($extensions as $ext => $enabled)
                        <div class="col-6 col-md-3 col-lg-2">
                            <div class="d-flex align-items-center justify-content-between p-2 border rounded {{ $enabled ? 'bg-light' : 'bg-danger-subtle' }}">
                                <span class="fw-bold">{{ ucfirst($ext) }}</span>
                                @if($enabled)
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                @else
                                    <i class="bi bi-x-circle-fill text-danger"></i>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- System Logs -->
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-dark text-white">
            <div class="card-header bg-dark border-secondary py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-white"><i class="bi bi-terminal-fill"></i> Recent System Logs (Last 20 Lines)</h6>
                <span class="badge bg-secondary">{{ $logSize }}</span>
            </div>
            <div class="card-body font-monospace p-0" style="max-height: 400px; overflow-y: auto; background-color: #1e1e1e;">
                <div class="p-3">
                    @forelse($recentLogs as $log)
                        <div class="text-break border-bottom border-secondary pb-1 mb-1 text-light small" style="opacity: 0.9;">
                            {{ $log }}
                        </div>
                    @empty
                        <div class="text-muted text-center py-4">No logs found or log file is empty.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
