<div class="card h-100 border-0 shadow-sm transition-hover">
    <div class="position-relative bg-white rounded-top overflow-hidden" style="aspect-ratio: 4/3;">
        @if($product->image)
            <img src="{{ $product->image }}" class="w-100 h-100 object-fit-cover" alt="{{ $product->name }}">
        @else
            <div class="d-flex align-items-center justify-content-center h-100 text-muted bg-light">
                <i class="bi bi-box fs-1 opacity-25"></i>
            </div>
        @endif
        
        <!-- Discount Badge -->
        @if($product->has_discount)
            <div class="position-absolute top-0 start-0 m-2">
                <span class="badge bg-danger rounded-pill px-2 py-1 shadow-sm font-monospace">{{ $product->discount_percentage }}% OFF</span>
            </div>
        @endif
    </div>
    <div class="card-body d-flex flex-column">
        <small class="text-muted mb-1">{{ $product->category->name ?? 'General' }}</small>
        <h6 class="fw-bold mb-3 text-truncate">{{ $product->name }}</h6>
        
        <div class="mb-3">
            @if($product->has_discount)
                <div class="d-flex flex-wrap align-items-baseline gap-1">
                    <span class="fw-bold text-danger fs-5">৳{{ $product->effective_price }}</span>
                    <span class="text-muted text-decoration-line-through small">৳{{ $product->price }}</span>
                </div>
            @else
                <span class="fs-5 fw-bold text-primary">৳{{ $product->price }}</span>
            @endif
        </div>

        <div class="row g-2 mt-auto position-relative" style="z-index: 2;">
            <div class="col-6">
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button type="submit" class="btn btn-outline-primary rounded-pill fw-bold btn-equal" title="Add to Cart">
                        <i class="bi bi-cart-plus me-1"></i> Add
                    </button>
                </form>
            </div>
            <div class="col-6">
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="buy_now" value="1">
                    <button type="submit" class="btn btn-primary rounded-pill fw-bold btn-equal">
                        Buy
                    </button>
                </form>
            </div>
        </div>
    </div>
    <a href="{{ route('shop.show', $product->slug) }}" class="stretched-link"></a>
</div>
