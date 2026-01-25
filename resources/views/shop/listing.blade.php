@extends('layouts.app')

@section('title', $title . ' | OlexaBD')
@section('meta_description', 'Shop ' . $title . ' at OlexaBD. Great selection, official warranty, and fast delivery across Bangladesh.')

@section('content')
<div class="bg-light py-5">
    <div class="container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('shop.index') }}" class="text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                    </ol>
                </nav>
                <h2 class="fw-bold mb-0">{{ $title }}</h2>
            </div>
        </div>

        <!-- Subcategories -->
        @if(isset($children) && $children->count() > 0)
        <div class="row g-3 mb-5">
            @foreach($children as $child)
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('shop.category', $child) }}" class="card h-100 border-0 shadow-sm text-decoration-none transition-hover text-center py-3 bg-white align-items-center">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-2 text-primary" style="width: 50px; height: 50px;">
                        @if($child->image)
                            <img src="{{ $child->image }}" class="rounded-circle" width="50" height="50" style="object-fit:cover;">
                        @else
                            <i class="bi bi-grid fs-4"></i>
                        @endif
                    </div>
                    <h6 class="text-dark fw-bold mb-0 small">{{ $child->name }}</h6>
                    <small class="text-muted" style="font-size: 0.75rem;">{{ $child->products_count }} Items</small>
                </a>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Product Grid -->
        @if($products->count() > 0)
            <div class="row g-4 mb-5">
                @foreach($products as $product)
                <div class="col-md-3 col-6">
                    @include('partials.product-card', ['product' => $product])
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-basket3 display-1 text-muted opacity-25"></i>
                <h3 class="mt-3 text-muted">No products found here.</h3>
                <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill mt-3 px-4">Continue Shopping</a>
            </div>
        @endif
    </div>
</div>

<style>
    .object-fit-cover { object-fit: cover; }
    .transition-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .transition-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }
</style>
@endsection
