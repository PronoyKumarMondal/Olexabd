@extends('layouts.admin')

@section('header', 'Order Details: ' . $order->order_code)

@section('content')
<div class="row">
    <!-- Order Information -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 fw-bold">Items Ordered</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th class="text-end pe-4">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        @if($item->product && $item->product->image)
                                            <img src="{{ $item->product->image }}" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $item->product->name ?? 'Unknown Product' }}</div>
                                            <small class="text-muted">{{ $item->product->sku ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>৳{{ number_format($item->unit_price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td class="text-end pe-4 fw-bold">৳{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                            </tr>
                            @endforeach
                            <tr class="bg-light">
                                <td colspan="3" class="text-end fw-bold">Subtotal</td>
                                <td class="text-end pe-4 fw-bold">৳{{ number_format($order->total_amount + $order->discount_amount, 2) }}</td>
                            </tr>
                            @if($order->discount_amount > 0)
                            <tr class="bg-light text-success">
                                <td colspan="3" class="text-end fw-bold">Discount ({{ $order->coupon_code }})</td>
                                <td class="text-end pe-4 fw-bold">-৳{{ number_format($order->discount_amount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="bg-light">
                                <td colspan="3" class="text-end fw-bold">Grand Total</td>
                                <td class="text-end pe-4 fw-bold fs-5">৳{{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 fw-bold">Transaction Details</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small text-uppercase">Payment Method</label>
                        <div class="fw-bold">
                            {{ ucfirst($order->payment_method) }}
                            <span class="ms-2 badge {{ $order->source === 'web' ? 'bg-light text-dark border' : 'bg-info text-dark' }}">
                                {{ strtoupper($order->source) }}
                            </span>
                        </div>
                    </div>
                    @if($order->transaction_id)
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small text-uppercase">Manual Payment</label>
                        <div>
                            <span class="badge bg-light text-dark border me-1">TrxID: {{ $order->transaction_id }}</span>
                            <span class="badge bg-light text-dark border">No: {{ $order->payment_number }}</span>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small text-uppercase">Payment Status</label>
                        <div>
                            @if($order->payment_status === 'paid')
                                <span class="badge bg-success">Paid</span>
                            @elseif($order->payment_status === 'partial')
                                <span class="badge bg-info text-dark">Partial Paid</span>
                                <div class="small mt-1 text-muted">adv. paid</div>
                            @else
                                <span class="badge bg-warning text-dark">Unpaid</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="text-muted small text-uppercase">Shipping Address</label>
                        <div class="fw-bold">{{ $order->shipping_address }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Management -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 fw-bold">Order Management</div>
            <div class="card-body">
                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small">Update Order Status</label>
                        <select name="status" class="form-select form-select-lg mb-3" @if(!Auth::guard('admin')->user()->isSuperAdmin() && !Auth::guard('admin')->user()->hasPermission('order_edit')) disabled @endif>
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending Payment</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        <div class="form-text small mb-3">
                            <i class="bi bi-info-circle"></i> 
                            'Delivered' orders usually auto-complete after 3 days.
                        </div>
                    </div>

                    @if(Auth::guard('admin')->user()->isSuperAdmin() || Auth::guard('admin')->user()->hasPermission('order_edit'))
                        <button type="submit" class="btn btn-primary w-100">Update Status</button>
                    @else
                        <div class="alert alert-warning small">
                            You do not have permission to update orders.
                        </div>
                    @endif
                </form>

                @if($order->payment_status === 'unpaid' && $order->transaction_id && (Auth::guard('admin')->user()->isSuperAdmin() || Auth::guard('admin')->user()->hasPermission('order_edit')))
                <hr>
                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="mark_paid" value="1">
                    <input type="hidden" name="status" value="processing"> 
                    
                    <div class="d-grid">
                        <small class="text-muted mb-2 text-center">Verify Transaction: <strong>{{ $order->transaction_id }}</strong></small>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check2-circle"></i> Verify & Mark Paid
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 fw-bold">Customer</div>
            <div class="card-body d-flex align-items-center">
                <div class="bg-light rounded-circle p-3 me-3">
                    <i class="bi bi-person fs-4 text-muted"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold">{{ $order->user->name ?? 'Guest User' }}</h6>
                    <small class="text-muted">{{ $order->user->email ?? 'No Email' }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
