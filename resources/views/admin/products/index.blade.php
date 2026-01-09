@extends('layouts.admin')

@section('header', 'Products')

@section('content')
@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <form action="{{ route('admin.products.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by Code or Name" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="low_stock" {{ request('status') === 'low_stock' ? 'selected' : '' }}>Low Stock (< 10)</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-end mb-4">
    @can('create', App\Models\Product::class)
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Add New Product</a>
    @endcan
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Image</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Views</th>
                        <th>Last Updated</th>
                        <th>Updated By</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td class="ps-4 text-muted small">#{{ $product->id }}</td>
                        <td>
                            @if($product->image)
                                <img src="{{ $product->image }}" class="rounded border" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <span class="badge bg-light text-secondary border">No Img</span>
                            @endif
                        </td>
                        <td class="text-primary font-monospace">{{ $product->code }}</td>
                        <td>
                            <div class="fw-bold">{{ $product->name }}</div>
                            <small class="text-muted d-block">{{ Str::limit($product->description, 30) }}</small>
                        </td>
                        <td>{{ $product->category->name ?? '-' }}</td>
                        <td class="fw-bold">৳{{ $product->price }}</td>
                        <td>
                            @if($product->stock < 10)
                                <span class="text-danger fw-bold">{{ $product->stock }}</span>
                            @else
                                {{ $product->stock }}
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info bg-opacity-10 text-info">
                                <i class="bi bi-eye-fill me-1"></i> {{ number_format($product->views) }}
                            </span>
                        </td>
                        <td>
                            <span class="small text-muted">{{ $product->updated_at->format('M d, Y h:i A') }}</span>
                        </td>
                        <td>
                            @if($product->updated_by)
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                    <i class="bi bi-person-circle me-1"></i> {{ $product->updater?->name ?? 'Admin' }}
                                </span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-end pe-4 text-nowrap">
                            <div class="d-flex justify-content-end gap-2">
                                @can('update', $product)
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                @endcan
                                
                                @can('delete', $product)
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                                @endcan
                            </div>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-3 py-3">
            {{ $products->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
