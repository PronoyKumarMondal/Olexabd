@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">Edit Banner</h2>
        <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>

    <div class="card shadow mb-4" style="max-width: 800px;">
        <div class="card-body">
            <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Current Image</label>
                    <div class="mb-2">
                        <img src="{{ $banner->image }}" class="img-thumbnail" style="max-height: 150px;">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Change Image (Optional)</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <div class="form-text text-primary">
                        <i class="bi bi-info-circle"></i> <strong>Ideal Size:</strong> 1920x450 pixels.
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ $banner->title }}">
                </div>

                 <div class="mb-4">
                    <label class="form-label fw-bold">Current Mobile Image</label>
                    @if($banner->mobile_image)
                        <div class="mb-2">
                            <img src="{{ $banner->mobile_image }}" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                    @else
                        <div class="text-muted small">No mobile specific image uploaded.</div>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Change Mobile Image (Optional)</label>
                    <input type="file" name="mobile_image" class="form-control" accept="image/*">
                    <div class="form-text text-primary">
                        <i class="bi bi-info-circle"></i> <strong>Ideal Size:</strong> 800x600 pixels.
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Badge Text</label>
                    <input type="text" name="badge_text" class="form-control" value="{{ $banner->badge_text }}" placeholder="e.g. New Arrival">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Link URL</label>
                    <input type="url" name="link" class="form-control" value="{{ $banner->link }}">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Display Order</label>
                        <input type="number" name="order" class="form-control" value="{{ $banner->order }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ $banner->is_active ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !$banner->is_active ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-4">Update Banner</button>
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-light ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
