@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">Add New Banner</h2>
        <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>

    <div class="card shadow mb-4" style="max-width: 800px;">
        <div class="card-body">
            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Banner Image <span class="text-danger">*</span></label>
                    <input type="file" name="image" class="form-control" accept="image/*" required>
                    <div class="form-text text-primary">
                        <i class="bi bi-info-circle"></i> <strong>Ideal Size:</strong> 1920x450 pixels (Desktop).
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Mobile Banner Image <span class="text-danger">*</span></label>
                    <input type="file" name="mobile_image" class="form-control" accept="image/*" required>
                    <div class="form-text text-primary">
                        <i class="bi bi-info-circle"></i> <strong>Ideal Size:</strong> 800x600 pixels (4:3 Ratio).<br>
                        Displayed on phones instead of the desktop banner.
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Title (Optional)</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Summer Sale">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Badge Text (Optional)</label>
                    <input type="text" name="badge_text" class="form-control" placeholder="e.g. New Arrival, 50% OFF">
                    <div class="form-text">Text shown in the small badge above the title.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Link URL (Optional)</label>
                    <input type="url" name="link" class="form-control" placeholder="e.g. https://olexabd.com/category/fridge">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Display Order</label>
                        <input type="number" name="order" class="form-control" value="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-4">Create Banner</button>
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-light ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
