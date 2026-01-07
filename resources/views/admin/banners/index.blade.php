@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">Banners</h2>
        @if($canAdd)
        <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Add New Banner
        </a>
        @else
        <button class="btn btn-secondary" disabled>
            <i class="bi bi-slash-circle me-2"></i>Limit Reached (Max 5)
        </button>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">Order</th>
                            <th style="width: 150px;">Image</th>
                            <th>Title</th>
                            <th>Link</th>
                            <th>Status</th>
                            <th style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banners as $banner)
                        <tr>
                            <td>{{ $banner->order }}</td>
                            <td>
                                <img src="{{ $banner->image }}" alt="Banner" class="img-thumbnail" style="height: 60px; object-fit: cover;">
                            </td>
                            <td>{{ $banner->title ?? 'No Title' }}</td>
                            <td>
                                @if($banner->link)
                                <a href="{{ $banner->link }}" target="_blank" class="text-decoration-none">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>View
                                </a>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($banner->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No banners found. Add one to get started!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
