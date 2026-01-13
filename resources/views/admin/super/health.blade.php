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

    <!-- Detailed Directory Permissions -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
             <div class="card-header bg-white py-3 border-bottom border-secondary border-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-folder-check"></i> Filesystem Permissions</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Directory</th>
                            <th class="text-end">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissions as $path => $isWritable)
                        <tr>
                            <td class="font-monospace">{{ $path }}</td>
                            <td class="text-end">
                                @if($isWritable)
                                    <span class="badge bg-success bg-opacity-75">Writable</span>
                                @else
                                    <span class="badge bg-danger">Not Writable</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Database Analytics -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom border-dark border-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-graph-up"></i> Database Analytics</h6>
            </div>
            <div class="card-body p-0">
                 <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Failed Jobs</span>
                    @if($failedJobsCount > 0)
                        <span class="badge bg-danger fs-6">{{ $failedJobsCount }}</span>
                    @else
                        <span class="badge bg-success bg-opacity-75">0 (Healthy)</span>
                    @endif
                </div>
                <div class="bg-light p-2 fw-bold small text-uppercase text-muted border-bottom">Top 5 Largest Tables</div>
                <table class="table table-sm mb-0 small">
                    <thead>
                        <tr>
                            <th>Table</th>
                            <th class="text-end">Rows</th>
                            <th class="text-end">Size (MB)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tableStats as $stat)
                        <tr>
                            <td>{{ $stat->table_name }}</td>
                            <td class="text-end">{{ number_format($stat->table_rows) }}</td>
                            <td class="text-end fw-bold">{{ $stat->size_mb }} MB</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Technical Insights -->
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body">
                <div class="row g-4 text-center">
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block text-uppercase ls-1 fw-bold mb-1">Current Version (Git)</small>
                        <div class="font-monospace text-primary text-break">{{ $gitInfo }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block text-uppercase ls-1 fw-bold mb-1">Registered Routes</small>
                        <div class="h4 fw-bold mb-0">{{ number_format($routeCount) }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block text-uppercase ls-1 fw-bold mb-1">Maintenance Mode</small>
                        @if($maintenanceMode)
                            <span class="badge bg-warning text-dark">ACTIVE</span>
                        @else
                            <span class="badge bg-success">LIVE</span>
                        @endif
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block text-uppercase ls-1 fw-bold mb-1">Active Sessions (1h)</small>
                        <div class="h4 fw-bold mb-0">{{ $activeSessions }}</div>
                    </div>
                </div>
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
                <h6 class="mb-0 fw-bold text-white"><i class="bi bi-terminal-fill"></i> Recent System Logs (Last 100 Lines)</h6>
                <span class="badge bg-secondary">{{ $logSize }}</span>
            </div>
            <div class="card-body font-monospace p-0" style="max-height: 600px; overflow-y: auto; background-color: #1e1e1e;">
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
