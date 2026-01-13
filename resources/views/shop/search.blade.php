@extends('layouts.app')

@section('title', 'Search results for "' . $query . '" | OlexaBD')
@section('meta_description', 'Search results for ' . $query . ' at OlexaBD.')

@section('content')
<div class="container py-5">
    <!-- Search Header -->
    <div class="mb-5 text-center">
        <h1 class="fw-bold mb-3">Search Results</h1>
        <p class="text-muted fs-5">Showing results for <span class="text-primary fw-bold">"{{ $query }}"</span></p>
    </div>

    <!-- Results Grid -->
    @if($products->count() > 0)
        <div class="row g-4 mb-5">
            @foreach($products as $product)
            <div class="col-md-3 col-6">
                <div class="card h-100 border border-light shadow-hover transition-all product-card">
                    <div class="position-relative bg-light rounded-top" style="height: 200px;">
                        @if($product->image)
                            <img src="{{ $product->image }}" class="w-100 h-100 object-fit-cover" alt="{{ $product->name }}">
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                <i class="bi bi-box fs-1 opacity-25"></i>
                            </div>
                        @endif
                    </div>
                    <div class="card-body d-flex flex-column">
                        <small class="text-muted mb-1">{{ $product->category->name ?? 'General' }}</small>
                        <h6 class="fw-bold mb-3 text-truncate">{{ $product->name }}</h6>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fs-5 fw-bold text-dark">৳{{ $product->price }}</span>
                        </div>
                        
                        <div class="d-flex gap-2 mt-auto position-relative" style="z-index: 2;">
                            <form action="{{ route('cart.add') }}" method="POST" class="w-100">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit" class="btn btn-outline-primary w-100 rounded-pill btn-sm fw-bold" title="Add to Cart">
                                    <i class="bi bi-cart-plus"></i> Add
                                </button>
                            </form>
                            <form action="{{ route('cart.add') }}" method="POST" class="w-100">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="buy_now" value="1">
                                <button type="submit" class="btn btn-primary w-100 rounded-pill btn-sm fw-bold">
                                    Buy
                                </button>
                            </form>
                        </div>
                    </div>
                    <a href="{{ route('shop.show', $product->slug) }}" class="stretched-link"></a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $products->appends(['query' => $query])->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-search fs-1 text-muted opacity-25 display-1"></i>
            </div>
            <h3 class="fw-bold text-muted mb-3">No products found</h3>
            <p class="text-muted mb-4">We couldn't find any products matching your search. Try adjusting your keywords.</p>
            <a href="{{ route('shop.products') }}" class="btn btn-primary rounded-pill px-5 py-2">Browse All Products</a>
        </div>
    @endif

    <!-- Recommendations Section -->
    @if($recommendedProducts->count() > 0)
    <div class="mt-5 pt-5 border-top">
        <h4 class="fw-bold mb-4">You May Also Like</h4>
        <div class="row g-4">
            @foreach($recommendedProducts as $product)
            <div class="col-md-3 col-6">
                <div class="card h-100 border-0 shadow-sm product-card">
                    <div class="position-relative overflow-hidden bg-light rounded-top" style="height: 180px;">
                        @if($product->image)
                            <img src="{{ $product->image }}" class="w-100 h-100 object-fit-cover transition-transform" alt="{{ $product->name }}">
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                <i class="bi bi-image fs-1 opacity-50"></i>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">{{ $product->category->name ?? 'Appliance' }}</small>
                        <h6 class="card-title fw-bold text-truncate mb-2"><a href="{{ route('shop.show', $product->slug) }}" class="text-dark text-decoration-none stretched-link">{{ $product->name }}</a></h6>
                        <h5 class="text-primary fw-bold mb-0">৳{{ $product->price }}</h5>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<style>
    .object-fit-cover { object-fit: cover; }
    .transition-all { transition: all 0.3s ease; }
    .shadow-hover:hover { box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; transform: translateY(-5px); border-color: transparent !important; }
    .transition-transform { transition: transform 0.3s ease; }
    .product-card:hover .transition-transform { transform: scale(1.05); }
</style>
@endsection
