@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="text-center mb-5">
                <h1 class="fw-bold mb-3">Track Your Order</h1>
                <p class="text-muted">Enter your Order ID to check the current status of your shipment.</p>
            </div>

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-body p-5">
                    <form action="{{ route('orders.track.submit') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold">Order Code</label>
                            <input type="text" name="order_code" class="form-control form-control-lg rounded-pill px-4" placeholder="e.g. A1B2C3" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold fs-5 shadow-sm">
                            Track Order <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted small">Need help? <a href="#" class="text-decoration-none">Contact Support</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
