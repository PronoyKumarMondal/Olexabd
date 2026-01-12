@extends('layouts.admin')

@section('title', 'Edit Channel')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Channel</h1>
        <a href="{{ route('admin.channels.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="card shadow mb-4" style="max-width: 600px;">
        <div class="card-body">
            <form action="{{ route('admin.channels.update', $channel) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Channel Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $channel->name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Slug (Code)</label>
                    <input type="text" name="slug" class="form-control" value="{{ old('slug', $channel->slug) }}" required>
                </div>

                <div class="mb-3 form-check">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" class="form-check-input" name="is_active" value="1" id="isActive" {{ $channel->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="isActive">Active</label>
                </div>

                <button type="submit" class="btn btn-primary w-100">Update Channel</button>
            </form>
        </div>
    </div>
</div>
@endsection
