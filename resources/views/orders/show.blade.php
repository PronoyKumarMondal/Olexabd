@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-0">Order #{{ $order->order_code }}</h2>
            <p class="text-muted">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
        </div>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left me-2"></i>Back to History
        </a>
    </div>

    <div class="row g-4">
        <!-- Order Items -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Items</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-borderless align-middle mb-0">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="ps-4 py-3">Product</th>
                                <th class="py-3 text-center">Price</th>
                                <th class="py-3 text-center">Qty</th>
                                <th class="pe-4 py-3 text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr class="border-bottom">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        @if($item->product && $item->product->image)
                                            <img src="{{ $item->product->image }}" class="rounded-3 object-fit-cover" width="50" height="50">
                                        @else
                                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 50px;">
                                                <i class="bi bi-box"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">{{ $item->product->name ?? 'Product Unavailable' }}</h6>
                                            @if($item->product)
                                            <a href="{{ route('shop.show', $item->product->slug) }}" class="text-decoration-none small">View Product</a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">৳{{ $item->price }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="pe-4 text-end fw-bold">৳{{ $item->price * $item->quantity }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white p-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-bold">৳{{ $order->total_amount }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Shipping</span>
                        @if($order->delivery_charge > 0)
                            <span class="fw-bold">৳{{ $order->delivery_charge }}</span>
                        @else
                            <span class="text-success fw-bold">Free</span>
                        @endif
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="h5 fw-bold mb-0">Total</span>
                        <span class="h5 fw-bold mb-0 text-primary">৳{{ $order->total_amount }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Info -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Order Status</h5>
                    <div class="mb-4">
                        @if($order->status == 'completed')
                            <div class="d-flex align-items-center gap-2 text-success fw-bold">
                                <i class="bi bi-check-circle-fill fs-4"></i> Completed
                            </div>
                            <p class="text-muted small mt-1">Your order has been delivered.</p>
                        @elseif($order->status == 'pending')
                            <div class="d-flex align-items-center gap-2 text-warning fw-bold">
                                <i class="bi bi-clock-history fs-4"></i> Pending
                            </div>
                            <p class="text-muted small mt-1">We are processing your order.</p>
                        @else
                            <div class="d-flex align-items-center gap-2 text-primary fw-bold">
                                <i class="bi bi-truck fs-4"></i> {{ ucfirst($order->status) }}
                            </div>
                        @endif
                    </div>
                    
                    <h6 class="fw-bold mb-2">Payment Status</h6>
                    @if($order->payment_status == 'paid')
                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Paid via {{ $order->payment_method ?? 'Gateway' }}</span>
                    @else
                        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Unpaid</span>
                    @endif
                </div>
            </div>

            <!-- Shipping Info -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Shipping Address</h5>
                    <p class="text-muted mb-0" style="white-space: pre-line;">{{ $order->shipping_address }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
