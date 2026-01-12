@extends('layouts.admin')

@section('title', 'Manage Channels')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Sales Channels</h1>
        <a href="{{ route('admin.channels.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add New Channel
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Channels</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($channels as $channel)
                        <tr>
                            <td>{{ $channel->id }}</td>
                            <td><span class="fw-bold">{{ $channel->name }}</span></td>
                            <td><code>{{ $channel->slug }}</code></td>
                            <td>
                                @if($channel->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $channel->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.channels.edit', $channel) }}" class="btn btn-sm btn-info text-white">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.channels.destroy', $channel) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure? This may affect orders linked to this channel.');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No channels found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
