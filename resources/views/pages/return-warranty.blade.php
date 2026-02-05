@extends('layouts.app')

@section('title', 'Return & Warranty Policy | OlexaBD')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="fw-bold mb-4">Return & Warranty Policy</h1>
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h4 class="fw-bold text-primary mb-3">Return Policy</h4>
                    <p>At OlexaBD, we want you to be completely satisfied with your purchase. If you receive a defective or incorrect item, you may return it under the following conditions:</p>
                    <ul>
                        <li>Returns must be requested within <strong>3 days</strong> of delivery.</li>
                        <li>The product must be unused, in its original packaging, and with all accessories and tags intact.</li>
                        <li>Proof of purchase (Order ID or Invoice) is required.</li>
                    </ul>
                    <p class="mb-0">To initiate a return, please contact our support team at <a href="mailto:support@olexabd.com">support@olexabd.com</a>.</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h4 class="fw-bold text-primary mb-3">Warranty Claim</h4>
                    <p>We provide official brand warranty for eligible products. The warranty period and terms vary by manufacturer.</p>
                    <ul>
                        <li>Warranties cover manufacturing defects only.</li>
                        <li>Physical damage, water damage, or misuse is not covered.</li>
                        <li>Please retain your warranty card and invoice to claim warranty services.</li>
                    </ul>
                    <div class="alert alert-light border mt-3">
                        <strong>Note:</strong> OlexaBD acts as a facilitator for warranty claims. The final decision rests with the brand's authorized service center.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
