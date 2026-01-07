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
                        <th>Code</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td class="ps-4 text-muted small">#{{ $product->id }}</td>
                        <td class="text-primary font-monospace">{{ $product->code }}</td>
                        <td>
                            <div class="fw-bold">{{ $product->name }}</div>
                            <small class="text-muted d-block">{{ Str::limit($product->description, 30) }}</small>
                        </td>
                        <td>{{ $product->category->name ?? '-' }}</td>
                        <td class="fw-bold">৳{{ $product->price }}</td>
                        <td>{{ $product->stock }}</td>
                        <td class="text-end pe-4">
                            @can('update', $product)
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary me-1">Edit</a>
                            @endcan
                            
                            @can('delete', $product)
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
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
            {{ $products->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
