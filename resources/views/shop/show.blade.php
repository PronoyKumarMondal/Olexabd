@extends('layouts.app')

@section('content')
<div class="bg-light py-5">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('shop.index') }}" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shop.products') }}" class="text-decoration-none">Shop</a></li>
                @if($product->category)
                    <li class="breadcrumb-item"><a href="{{ route('shop.category', $product->category->slug) }}" class="text-decoration-none">{{ $product->category->name }}</a></li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="row g-5">
            <!-- Product Image -->
            <div class="col-lg-6">
                <div class="bg-white rounded-4 shadow-sm p-4 text-center">
                    @if($product->image)
                        <img src="{{ $product->image }}" class="img-fluid rounded-3" style="max-height: 500px; object-fit: contain;" alt="{{ $product->name }}">
                    @else
                        <div class="d-flex align-items-center justify-content-center bg-light rounded-3" style="height: 400px;">
                            <i class="bi bi-image display-1 text-muted opacity-25"></i>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Product Details -->
            <div class="col-lg-6">
                <div class="ps-lg-4">
                    <span class="badge bg-primary bg-opacity-10 text-primary mb-2 px-3 py-2 rounded-pill">{{ $product->category->name ?? 'General' }}</span>
                    <h1 class="fw-bold mb-3 display-6">{{ $product->name }}</h1>
                    
                    <div class="d-flex align-items-center mb-4">
                        <h2 class="text-primary fw-bold mb-0 me-3">৳{{ $product->price }}</h2>
                        @if($product->stock > 0)
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">In Stock</span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Out of Stock</span>
                        @endif
                    </div>

                    <p class="text-muted lead mb-4">{{ $product->description }}</p>

                    <!-- Actions -->
                    <div class="d-flex gap-3 mb-5">
                        <form action="{{ route('cart.add') }}" method="POST" class="flex-grow-1">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button class="btn btn-outline-primary w-100 rounded-pill py-3 fw-bold shadow-sm action-btn">
                                <i class="bi bi-cart-plus me-2"></i> Add to Cart
                            </button>
                        </form>
                        <form action="{{ route('checkout.init') }}" method="POST" class="flex-grow-1">
                            @csrf
                            <input type="hidden" name="amount" value="{{ $product->price }}">
                            <input type="hidden" name="order_id" value="ORD-{{ rand(1000,9999) }}">
                            <button class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-lg action-btn">
                                <i class="bi bi-lightning-charge me-2"></i> Buy Now
                            </button>
                        </form>
                    </div>

                    <!-- Additional Info / Specs -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>Product Details</h5>
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-muted w-25">Product Code</td>
                                        <td class="fw-bold">#{{ str_pad($product->id, 6, '0', STR_PAD_LEFT) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Category</td>
                                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Availability</td>
                                        <td class="text-{{ $product->stock > 0 ? 'success' : 'danger' }} fw-bold">
                                            {{ $product->stock > 0 ? 'Available' : 'Unavailable' }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if(isset($relatedProducts) && $relatedProducts->count() > 0)
        <div class="mt-5 pt-5 border-top">
            <h3 class="fw-bold mb-4">You May Also Like</h3>
            <div class="row g-4">
                @foreach($relatedProducts as $related)
                <div class="col-md-3 col-6">
                    <div class="card h-100 border-0 shadow-sm text-decoration-none">
                        <div class="position-relative bg-light rounded-top" style="height: 200px;">
                            @if($related->image)
                                <img src="{{ $related->image }}" class="w-100 h-100 object-fit-cover" alt="{{ $related->name }}">
                            @endif
                        </div>
                        <div class="card-body">
                            <h6 class="fw-bold text-truncate text-dark">{{ $related->name }}</h6>
                            <h5 class="text-primary fw-bold mb-0">৳{{ $related->price }}</h5>
                            <a href="{{ route('shop.show', $related->slug) }}" class="stretched-link"></a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

<style>
    .action-btn { transition: transform 0.2s; }
    .action-btn:hover { transform: translateY(-2px); }
</style>
@endsection
