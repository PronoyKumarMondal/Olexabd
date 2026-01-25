<div class="card h-100 border-0 shadow-sm transition-hover">
    <div class="position-relative bg-white rounded-top" style="height: 220px;">
        @if($product->image)
            <img src="{{ $product->image }}" class="w-100 h-100 object-fit-cover" alt="{{ $product->name }}">
        @else
            <div class="d-flex align-items-center justify-content-center h-100 text-muted bg-light">
                <i class="bi bi-box fs-1 opacity-25"></i>
            </div>
        @endif
        
        <!-- Optional Badges (e.g. Hot/New) can be passed as variables or logic here if needed -->
        <!-- For now, simplifying to standard image view -->
    </div>
    <div class="card-body d-flex flex-column">
        <small class="text-muted mb-1">{{ $product->category->name ?? 'General' }}</small>
        <h6 class="fw-bold mb-3 text-truncate">{{ $product->name }}</h6>
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="fs-5 fw-bold text-primary">৳{{ $product->price }}</span>
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
