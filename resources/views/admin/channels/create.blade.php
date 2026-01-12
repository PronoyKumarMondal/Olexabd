@extends('layouts.admin')

@section('title', 'Create Channel')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Channel</h1>
        <a href="{{ route('admin.channels.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="card shadow mb-4" style="max-width: 600px;">
        <div class="card-body">
            <form action="{{ route('admin.channels.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Channel Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. Facebook Page, WhatsApp, Physical Store">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Slug (Code)</label>
                    <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" required placeholder="e.g. facebook_page">
                    <small class="text-muted">Unique identifier for system use.</small>
                </div>

                <div class="mb-3 form-check">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" class="form-check-input" name="is_active" value="1" id="isActive" checked>
                    <label class="form-check-label" for="isActive">Active</label>
                </div>

                <button type="submit" class="btn btn-primary w-100">Create Channel</button>
            </form>
        </div>
    </div>
</div>
@endsection
