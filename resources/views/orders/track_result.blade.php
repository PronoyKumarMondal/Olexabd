@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h2 class="fw-bold mb-0">Tracking Result</h2>
                <a href="{{ route('orders.track') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    Track Another
                </a>
            </div>

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-5">
                <div class="card-header bg-primary text-white p-4 text-center">
                    <h5 class="mb-0 opacity-75 text-uppercase ls-1" style="letter-spacing: 2px;">Order #{{ $order->order_code }}</h5>
                    <h2 class="fw-bold mt-2 display-6">{{ ucfirst($order->status) }}</h2>
                </div>
                <div class="card-body p-5">
                    <!-- Progress Bar -->
                    <div class="position-relative m-4">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $order->status == 'completed' ? '100' : ($order->status == 'shipped' ? '66' : '33') }}%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="position-absolute top-0 start-0 translate-middle btn btn-sm btn-success rounded-pill" style="width: 2rem; height:2rem;">1</div>
                        <div class="position-absolute top-0 start-50 translate-middle btn btn-sm {{ in_array($order->status, ['shipped', 'completed']) ? 'btn-success' : 'btn-secondary' }} rounded-pill" style="width: 2rem; height:2rem;">2</div>
                        <div class="position-absolute top-0 start-100 translate-middle btn btn-sm {{ $order->status == 'completed' ? 'btn-success' : 'btn-secondary' }} rounded-pill" style="width: 2rem; height:2rem;">3</div>
                        
                        <div class="d-flex justify-content-between mt-3 text-muted fw-bold small">
                            <span>Processing</span>
                            <span>Shipped</span>
                            <span>Delivered</span>
                        </div>
                    </div>

                    <hr class="my-5">

                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Customer</h6>
                            <p class="fw-bold mb-0">{{ $order->user->name ?? 'Guest' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Order Date</h6>
                            <p class="fw-bold mb-0">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Total Amount</h6>
                            <p class="fw-bold mb-0 text-primary">৳{{ $order->total_amount }}</p>
                        </div>
                         <div class="col-md-6">
                            <h6 class="text-muted mb-2">Tracking ID</h6>
                            <p class="fw-bold mb-0 font-monospace">TRK-{{ $order->created_at->format('Ymd') }}-{{ $order->id }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
