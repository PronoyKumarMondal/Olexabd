@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Order History</h2>

    @if($orders->count() > 0)
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Order ID</th>
                            <th class="py-3">Date</th>
                            <th class="py-3">Total</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td class="ps-4 fw-bold">#{{ $order->id }}</td>
                            <td class="text-muted">{{ $order->created_at->format('d M Y') }}</td>
                            <td class="fw-bold">৳{{ $order->total_amount }}</td>
                            <td>
                                @if($order->status == 'completed')
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Completed</span>
                                @elseif($order->status == 'pending')
                                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">Pending</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    View Details
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    @else
        <div class="text-center py-5 bg-white rounded-4 shadow-sm">
            <div class="mb-3">
                <i class="bi bi-bag-x fs-1 text-muted opacity-50"></i>
            </div>
            <h4 class="fw-bold text-muted">No orders found</h4>
            <p class="text-muted mb-4">You haven't placed any orders yet.</p>
            <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill px-4">Start Shopping</a>
        </div>
    @endif
</div>
@endsection
