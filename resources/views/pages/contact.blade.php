@extends('layouts.app')

@section('title', 'Contact Us | OlexaBD')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="fw-bold mb-4">Contact Us</h1>
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <p class="mb-4">We're here to help! If you have any questions, concerns, or feedback, please don't hesitate to reach out to us.</p>
                    
                    <div class="mb-4">
                        <h5 class="fw-bold"><i class="bi bi-geo-alt-fill text-primary me-2"></i>Address</h5>
                        <p class="mb-0 ms-4">Dhaka, Bangladesh</p>
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-bold"><i class="bi bi-envelope-fill text-primary me-2"></i>Email</h5>
                        <p class="mb-0 ms-4"><a href="mailto:support@olexabd.com" class="text-decoration-none">support@olexabd.com</a></p>
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-bold"><i class="bi bi-telephone-fill text-primary me-2"></i>Phone</h5>
                        <p class="mb-0 ms-4"><a href="tel:+8801700000000" class="text-decoration-none">+880 1700 000 000</a></p>
                    </div>

                    <div class="alert alert-info border-0 rounded-3">
                        <i class="bi bi-clock-history me-2"></i> Our support team is available Sunday to Thursday, 9:00 AM - 6:00 PM.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
