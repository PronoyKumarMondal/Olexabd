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
                            <div class="d-flex justify-content-between mb-4">
                                <span class="text-muted">Shipping</span>
                                <span class="text-success fw-bold">Free</span>
                            </div>
                            <hr class="border-secondary opacity-25">
                            <div class="d-flex justify-content-between mb-4">
                                <span class="h5 fw-bold mb-0">Total</span>
                                <span class="h5 fw-bold mb-0 text-primary">৳{{ $total }}</span>
                            </div>

                            <form action="{{ route('checkout.init') }}" method="POST">
                                @csrf
                                <input type="hidden" name="amount" value="{{ $total }}">
                                <input type="hidden" name="order_id" value="ORD-{{ rand(1000,9999) }}">
                                <button type="submit" class="btn btn-dark w-100 rounded-pill py-3 fw-bold shadow-lg">
                                    Proceed to Checkout
                                </button>
                            </form>
                            
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
