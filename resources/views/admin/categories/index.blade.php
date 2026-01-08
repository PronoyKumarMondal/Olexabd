@extends('layouts.admin')

@section('header', 'Categories')

@section('content')
@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <form action="{{ route('admin.categories.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search by Code or Name" value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-end mb-4">
    @can('create', App\Models\Category::class)
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
        Add New Category
    </button>
    @endcan
</div>

<!-- Create Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Parent Category</label>
                        <select name="parent_id" class="form-select">
                            <option value="">None (Main Category)</option>
                            @foreach($allCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Upload Image</label>
                            <input type="file" name="image_file" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">OR Image URL</label>
                            <input type="url" name="image_url" class="form-control" placeholder="https://">
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_active" class="form-check-input" id="activeCheck" checked value="1">
                        <label class="form-check-label" for="activeCheck">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal (Dynamic) -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editCategoryForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Parent Category</label>
                        <select name="parent_id" id="editParentId" class="form-select">
                            <option value="">None (Main Category)</option>
                            @foreach($allCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Upload New Image</label>
                            <input type="file" name="image_file" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">OR Image URL</label>
                            <input type="url" name="image_url" id="editImageUrl" class="form-control" placeholder="https://">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Category</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">All Categories</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Products</th>
                        <th>Last Updated</th>
                        <th>Updated By</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                @if($category->image)
                                    <img src="{{ $category->image }}" class="rounded me-2" width="32" height="32" style="object-fit:cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <i class="bi bi-tag text-muted"></i>
                                    </div>
                                @endif
                                <div class="fw-bold">
                                    @if($category->parent)
                                        <span class="text-muted small fw-normal">{{ $category->parent->name }} <i class="bi bi-chevron-right" style="font-size:0.75em;"></i></span><br>
                                    @endif
                                    {{ $category->name }}
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $category->products_count }} items</span>
                        </td>
                         <td>
                            <span class="small text-muted">{{ $category->updated_at->format('M d, Y h:i A') }}</span>
                        </td>
                        <td>
                             @if($category->updated_by)
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                    <i class="bi bi-person-circle me-1"></i> {{ $category->updater->name ?? 'Admin' }}
                                </span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if($category->is_active)
                                <span class="badge bg-success-subtle text-success">Active</span>
                            @else
                                <span class="badge bg-light text-dark border">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                             @can('update', $category)
                            <button class="btn btn-sm btn-outline-primary me-1" onclick="openEditModal('{{ $category->code }}', '{{ addslashes($category->name) }}', '{{ $category->image }}', '{{ $category->parent_id }}')">
                                Edit
                            </button>
                            @endcan
                            
                            @can('delete', $category)
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-3 py-3">
            {{ $categories->withQueryString()->links() }}
        </div>
    </div>
</div>

<script>
    function openEditModal(code, name, imageUrl, parentId) {
        document.getElementById('editName').value = name;
        document.getElementById('editImageUrl').value = imageUrl;
        document.getElementById('editParentId').value = parentId || "";
        
        
        // Construct the URL using the code
        let url = "{{ route('admin.categories.update', ':code') }}";
        url = url.replace(':code', code);
        
        document.getElementById('editCategoryForm').action = url;
        
        new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
    }
</script>
@endsection
