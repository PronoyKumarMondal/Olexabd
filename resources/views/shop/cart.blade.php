@extends('layouts.app')

@section('content')
<div class="bg-light py-5">
    <div class="container">
        <h2 class="fw-bold mb-4">Shopping Cart</h2>

        @if(session('cart') && count(session('cart')) > 0)
            <div class="row g-4">
                <!-- Cart Items -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-0">
                            <!-- Desktop View -->
                            <div class="d-none d-md-block">
                                <table class="table table-borderless align-middle mb-0">
                                    <thead class="bg-light border-bottom">
                                        <tr>
                                            <th class="ps-4 py-3">Product</th>
                                            <th class="py-3">Price</th>
                                            <th class="py-3">Quantity</th>
                                            <th class="pe-4 py-3 text-end">Total</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $total = 0; @endphp
                                        @foreach(session('cart') as $id => $details)
                                            @php $total += $details['price'] * $details['quantity']; @endphp
                                            <tr class="border-bottom cart-item">
                                                <td class="ps-4 py-3">
                                                    <div class="d-flex align-items-center gap-3">
                                                        @if(isset($details['image']))
                                                            <img src="{{ $details['image'] }}" width="60" height="60" class="rounded-3 object-fit-cover" alt="{{ $details['name'] }}">
                                                        @else
                                                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted" style="width: 60px; height: 60px;">
                                                                <i class="bi bi-box"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <h6 class="fw-bold mb-0 text-dark">{{ $details['name'] }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3">৳{{ $details['price'] }}</td>
                                                <td class="py-3">
                                                    <input type="number" value="{{ $details['quantity'] }}" class="form-control form-control-sm text-center update-cart" data-id="{{ $id }}" style="width: 70px;" min="1">
                                                </td>
                                                <td class="pe-4 py-3 text-end fw-bold">৳{{ $details['price'] * $details['quantity'] }}</td>
                                                <td class="py-3 text-end pe-3">
                                                    <button class="btn btn-link text-danger p-0 remove-from-cart" data-id="{{ $id }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile View -->
                            <div class="d-md-none">
                                @foreach(session('cart') as $id => $details)
                                    <div class="p-3 border-bottom cart-item position-relative">
                                        <div class="d-flex gap-3">
                                            @if(isset($details['image']))
                                                <img src="{{ $details['image'] }}" width="80" height="80" class="rounded-3 object-fit-cover flex-shrink-0" alt="{{ $details['name'] }}">
                                            @else
                                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted flex-shrink-0" style="width: 80px; height: 80px;">
                                                    <i class="bi bi-box"></i>
                                                </div>
                                            @endif
                                            
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="fw-bold text-dark mb-0 pe-4">{{ $details['name'] }}</h6>
                                                    <button class="btn btn-link text-danger p-0 remove-from-cart position-absolute top-0 end-0 m-3" data-id="{{ $id }}">
                                                        <i class="bi bi-trash fs-5"></i>
                                                    </button>
                                                </div>
                                                <div class="text-primary fw-bold mb-3">৳{{ $details['price'] }}</div>
                                                
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <small class="text-muted">Qty:</small>
                                                        <input type="number" value="{{ $details['quantity'] }}" class="form-control form-control-sm text-center update-cart" data-id="{{ $id }}" style="width: 60px;" min="1">
                                                    </div>
                                                    <div class="fw-bold">
                                                        Total: ৳{{ $details['price'] * $details['quantity'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 position-sticky" style="top: 100px;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">Order Summary</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal</span>
                                <span class="fw-bold">৳{{ $total }}</span>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted small text-uppercase">Shipping Location</label>
                                @php
                                    $insideCharge = \App\Models\Setting::get('delivery_charge_inside_dhaka', 60);
                                    $outsideCharge = \App\Models\Setting::get('delivery_charge_outside_dhaka', 120);
                                    
                                    // Check Rules
                                    $hasFree = false;
                                    
                                    // 1. Global Free
                                    if(\App\Models\Setting::get('free_delivery_all')) $hasFree = true;
                                    
                                    // 2. Min Order
                                    $minOrder = \App\Models\Setting::get('free_delivery_over');
                                    // Recalculate subtotal here just to be safe or use variable if available. 
                                    // $total variable passed to view is actually subtotal in CartController loop but discounted later? Use View's $total + coupon amount to get true subtotal?
                                    // Cart View loop calculates $total.
                                    if(!$hasFree && $minOrder && $total >= $minOrder) $hasFree = true;

                                    // 3. Product Rules
                                    if(!$hasFree) {
                                        $productIds = array_keys(session('cart'));
                                        $products = \App\Models\Product::whereIn('id', $productIds)->with('category')->get();
                                        foreach($products as $p) {
                                            if($p->is_free_delivery || ($p->category && $p->category->is_free_delivery)) {
                                                $hasFree = true; break;
                                            }
                                        }
                                    }
                                @endphp

                                @if($hasFree)
                                    <div class="alert alert-success py-2 small mb-2">
                                        <i class="bi bi-gift-fill me-1"></i> Free Delivery Applied!
                                    </div>
                                    <input type="hidden" name="delivery_location" value="inside"> 
                                    <!-- Value doesn't matter for cost, but backend expects input -->
                                @else
                                    <div class="d-flex flex-column gap-2" id="delivery-options">
                                        <label class="card p-3 border shadow-sm cursor-pointer delivery-option selected-option">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="radio" name="delivery_radio" value="inside" class="form-check-input" checked onchange="updateTotal()">
                                                    <span>Inside Dhaka</span>
                                                </div>
                                                <span class="fw-bold">৳{{ $insideCharge }}</span>
                                            </div>
                                        </label>
                                        <label class="card p-3 border shadow-sm cursor-pointer delivery-option">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="radio" name="delivery_radio" value="outside" class="form-check-input" onchange="updateTotal()">
                                                    <span>Outside Dhaka</span>
                                                </div>
                                                <span class="fw-bold">৳{{ $outsideCharge }}</span>
                                            </div>
                                        </label>
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex justify-content-between mb-4">
                                <span class="text-muted">Subtotal</span>
                                <span class="fw-bold">৳{{ $total }}</span>
                            </div>
                            
                            <!-- Promo Code Section -->
                            @if(session('coupon'))
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Discount ({{ session('coupon')['code'] }})</span>
                                    <span>-৳{{ session('coupon')['amount'] }}</span>
                                </div>
                                <div class="mb-3">
                                    <form action="{{ route('cart.remove_promo') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0 text-decoration-none" style="font-size: 0.85rem;">Remove Coupon</button>
                                    </form>
                                </div>
                                @php $total = $total - session('coupon')['amount']; @endphp
                            @else
                                <div class="mb-3">
                                     <form action="{{ route('cart.apply_promo') }}" method="POST" class="input-group">
                                        @csrf
                                        <input type="text" name="code" class="form-control" placeholder="Promo Code" required>
                                        <button class="btn btn-outline-secondary" type="submit">Apply</button>
                                    </form>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between mb-4">
                                <span class="text-muted">Shipping</span>
                                <span class="text-dark fw-bold" id="shipping-display">
                                    @if($hasFree) Free @else ৳{{ $insideCharge }} @endif
                                </span>
                            </div>
                            <hr class="border-secondary opacity-25">
                            <div class="d-flex justify-content-between mb-4">
                                <span class="h5 fw-bold mb-0">Total</span>
                                <span class="h5 fw-bold mb-0 text-primary" id="final-total">
                                    ৳{{ max(0, $total + ($hasFree ? 0 : $insideCharge)) }}
                                </span>
                            </div>

                            <form action="{{ route('checkout.init') }}" method="POST" id="checkout-form">
                                @csrf
                                <input type="hidden" name="amount" value="{{ $total }}">
                                <input type="hidden" name="order_id" value="ORD-{{ rand(1000,9999) }}">
                                <input type="hidden" name="delivery_location" id="delivery-location-input" value="inside">
                                <button type="submit" class="btn btn-dark w-100 rounded-pill py-3 fw-bold shadow-lg">
                                    Proceed to Checkout
                                </button>
                            </form>
                            
                            @if(!$hasFree)
                            <script>
                                const insideCharge = {{ $insideCharge }};
                                const outsideCharge = {{ $outsideCharge }};
                                const currentTotal = {{ $total }}; // Subtotal - Discount
                                
                                function updateTotal() {
                                    const location = document.querySelector('input[name="delivery_radio"]:checked').value;
                                    const charge = location === 'inside' ? insideCharge : outsideCharge;
                                    
                                    // Update Hidden Input
                                    document.getElementById('delivery-location-input').value = location;
                                    
                                    // Update Display
                                    document.getElementById('shipping-display').innerText = '৳' + charge;
                                    document.getElementById('final-total').innerText = '৳' + (currentTotal + charge);
                                    
                                    // Highlight Selection
                                    document.querySelectorAll('.delivery-option').forEach(el => {
                                        el.classList.remove('border-primary', 'bg-primary-subtle');
                                    });
                                    document.querySelector('input[name="delivery_radio"]:checked').closest('.delivery-option').classList.add('border-primary', 'bg-primary-subtle');
                                }
                                
                                // Init
                                document.addEventListener('DOMContentLoaded', function() {
                                    updateTotal();
                                });
                            </script>
                            @endif
                            
                            <a href="{{ route('shop.index') }}" class="btn btn-link w-100 text-decoration-none mt-2 text-muted">
                                <i class="bi bi-arrow-left me-1"></i> Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm" style="width: 100px; height: 100px;">
                        <i class="bi bi-cart-x fs-1 text-muted opacity-50"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-muted mb-3">Your cart is empty</h3>
                <p class="text-muted mb-4">Looks like you haven't added anything to your cart yet.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill px-5 py-2 fw-bold">Start Shopping</a>
            </div>
        @endif
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
    $(".update-cart").change(function (e) {
        e.preventDefault();
        var ele = $(this);
        var container = ele.closest(".cart-item"); // Use closest to work for both tr and div
        $.ajax({
            url: '{{ route('cart.update') }}',
            method: "patch",
            data: {
                _token: '{{ csrf_token() }}', 
                id: ele.attr("data-id"), 
                quantity: ele.val() // Get value directly from the input that changed, mostly safest. Or find it in container.
            },
            success: function (response) {
               window.location.reload();
            }
        });
    });

    $(".remove-from-cart").click(function (e) {
        e.preventDefault();
        var ele = $(this);
        if(confirm("Are you sure want to remove?")) {
            $.ajax({
                url: '{{ route('cart.remove') }}',
                method: "DELETE",
                data: {
                    _token: '{{ csrf_token() }}', 
                    id: ele.attr("data-id")
                },
                success: function (response) {
                    window.location.reload();
                }
            });
        }
    });
</script>
@endsection
