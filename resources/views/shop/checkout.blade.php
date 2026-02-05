@extends('layouts.app')

@section('content')
<div class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-dark display-6">Secure Checkout</h2>
            <p class="text-muted">Complete your purchase safely and securely.</p>
        </div>

        <div class="row g-4 g-lg-5">
            <!-- Left Side: Form -->
            <div class="col-lg-7 col-xl-8">
                <form action="{{ route('checkout.place') }}" method="POST" id="checkoutForm">
                    @csrf
                    <input type="hidden" name="mode" value="{{ $mode }}">

                    <!-- 1. Customer Details -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                        <div class="card-header bg-white border-bottom p-4">
                            <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-person-circle me-2"></i>Contact Information</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-muted small text-uppercase">Full Name</label>
                                    <input type="text" name="name" class="form-control form-control-lg bg-light border-0" value="{{ auth()->user()->name }}" required placeholder="e.g. John Doe">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-muted small text-uppercase">Phone Number</label>
                                    <input type="tel" name="phone" class="form-control form-control-lg bg-light border-0" placeholder="01XXXXXXXXX" pattern="(01)[0-9]{9}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Shipping Address -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                        <div class="card-header bg-white border-bottom p-4">
                            <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-truck me-2"></i>Shipping Address</h5>
                        </div>
                        <div class="card-body p-4">
                             <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-medium text-muted small text-uppercase">Division</label>
                                    <select name="division" id="division" class="form-select form-select-lg bg-light border-0" required onchange="loadDistricts()">
                                        <option value="">Select...</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium text-muted small text-uppercase">District</label>
                                    <select name="district" id="district" class="form-select form-select-lg bg-light border-0" required disabled onchange="loadUpazilas()">
                                        <option value="">Select...</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium text-muted small text-uppercase">Area / Upazila</label>
                                    <select name="upazila" id="upazila" class="form-select form-select-lg bg-light border-0" required disabled onchange="calculateShipping()">
                                        <option value="">Select...</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-medium text-muted small text-uppercase">Post Code</label>
                                <input type="text" name="postcode" class="form-control form-control-lg bg-light border-0" placeholder="e.g. 1212" required>
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-medium text-muted small text-uppercase">Detailed Address</label>
                                <textarea name="address" class="form-control form-control-lg bg-light border-0" rows="3" placeholder="House #, Road #, Block/Sector, Ward No, etc." required></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Payment Method -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                         <div class="card-header bg-white border-bottom p-4">
                            <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-credit-card me-2"></i>Payment Method</h5>
                        </div>
                        <div class="card-body p-4">
                            
                            <div class="row g-3 mb-4">
                                <!-- COD Option -->
                                <div class="col-12 col-md-4">
                                    <input type="radio" class="btn-check" name="payment_method" id="method_cod" value="cod" checked onchange="updatePaymentUI()">
                                    <label class="btn btn-outline-light border text-start p-3 w-100 h-100 d-flex flex-column align-items-center justify-content-center gap-2 rounded-3 method-card" for="method_cod">
                                        <i class="bi bi-cash-coin fs-2 text-dark"></i>
                                        <span class="fw-bold text-dark">Cash on Delivery</span>
                                    </label>
                                </div>
                                <!-- bKash Option -->
                                <div class="col-12 col-md-4">
                                    <input type="radio" class="btn-check" name="payment_method" id="method_bkash" value="bkash" onchange="updatePaymentUI()">
                                    <label class="btn btn-outline-light border text-start p-3 w-100 h-100 d-flex flex-column align-items-center justify-content-center gap-2 rounded-3 method-card" for="method_bkash">
                                        <img src="https://searchvectorlogo.com/wp-content/uploads/2025/02/bkash-logo-vector.svg" height="40" alt="bKash">
                                        <span class="fw-bold text-dark">bKash Personal</span>
                                    </label>
                                </div>
                                <!-- Bank Option -->
                                <div class="col-12 col-md-4">
                                    <input type="radio" class="btn-check" name="payment_method" id="method_bank" value="bank" onchange="updatePaymentUI()">
                                    <label class="btn btn-outline-light border text-start p-3 w-100 h-100 d-flex flex-column align-items-center justify-content-center gap-2 rounded-3 method-card" for="method_bank">
                                        <i class="bi bi-bank fs-2 text-dark"></i>
                                        <span class="fw-bold text-dark">Bank Transfer</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Payment Details Section (Hidden Logic) -->
                            <div id="payment-details-section" class="alert alert-warning border-0 rounded-3 d-none fade show" role="alert">
                                <div class="d-flex">
                                    <i class="bi bi-info-circle-fill fs-5 me-3 flex-shrink-0"></i>
                                    <div>
                                        <div id="payment-instruction-text" class="mb-3 small">
                                            <!-- Dynamic Text -->
                                        </div>
                                        
                                        <div id="trx-inputs" class="d-none">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-medium small" for="payment_number">Sender Phone/Account <span class="text-danger">*</span></label>
                                                    <input type="text" name="payment_number" id="payment_number" class="form-control bg-white" placeholder="Enter number...">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-medium small">Transaction ID <span class="text-danger">*</span></label>
                                                    <input type="text" name="transaction_id" id="transaction_id" class="form-control bg-white" placeholder="Enter TrxID...">
                                                </div>
                                            </div>
                                            <div class="mt-2 text-end">
                                               <small class="text-muted fst-italic">This will be verified by admin.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-lg d-lg-none mb-5">
                        Place Order <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </form>
            </div>

            <!-- Right Side: Order Summary (Sticky) -->
            <div class="col-lg-5 col-xl-4">
                <div class="card border-0 shadow-lg rounded-4 position-sticky bg-white overflow-hidden" style="top: 100px; z-index: 10;">
                    <div class="card-header bg-primary text-white p-4 border-0">
                        <h5 class="fw-bold mb-0"><i class="bi bi-receipt me-2"></i>Order Summary</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="cart-items mb-4 custom-scrollbar" style="max-height: 300px; overflow-y: auto;">
                            @foreach($cart as $item)
                                <div class="d-flex align-items-center mb-3 pb-3 border-bottom border-light last-no-border">
                                    <div class="position-relative">
                                         <!-- Fallback Image if needed, or just icon -->
                                         <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="bi bi-box-seam text-secondary"></i>
                                         </div>
                                         <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark">
                                            {{ $item['quantity'] }}
                                         </span>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <h6 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 180px;">{{ $item['name'] }}</h6>
                                        <small class="text-muted">Unit: ৳{{ $item['price'] }}</small>
                                    </div>
                                    <span class="fw-bold text-dark">৳{{ $item['price'] * $item['quantity'] }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                             <span class="text-muted">Subtotal</span>
                             <span class="fw-bold">৳{{ $subtotal }}</span>
                        </div>
                        @if($discount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Discount</span>
                            <span>-৳{{ $discount }}</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Shipping</span>
                            <span class="fw-bold" id="shipping-display">...</span>
                        </div>
                        
                        <!-- Shipping Info Box -->
                         <div class="alert alert-info py-2 small mb-3 border-0 bg-opacity-10 bg-info d-none" id="shipping-info-box">
                            <i class="bi bi-info-circle me-1"></i> <span id="shipping-info-text"></span>
                        </div>

                        <hr class="border-secondary border-opacity-10 my-4">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="h5 fw-bold mb-0 text-dark">Total</span>
                            <span class="h4 fw-bold mb-0 text-primary" id="final-total">৳{{ max(0, $subtotal - $discount) }}</span>
                        </div>

                        <!-- Desktop Submit Button (Form Linked via JS or Form Attribute) -->
                        <button type="submit" form="checkoutForm" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-lg transition-transform hover-scale">
                            Confirmed Order <i class="bi bi-check-lg ms-2"></i>
                        </button>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted"><i class="bi bi-shield-lock me-1"></i> SSL Secure Payment</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .method-card:hover {
        border-color: var(--bs-primary) !important;
        background-color: rgba(var(--bs-primary-rgb), 0.05);
    }
    .btn-check:checked + .method-card {
        border-color: var(--bs-primary) !important;
        background-color: rgba(var(--bs-primary-rgb), 0.1);
        box-shadow: 0 0 0 2px rgba(var(--bs-primary-rgb), 0.2);
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .last-no-border:last-child {
        border-bottom: 0 !important;
        padding-bottom: 0 !important;
        margin-bottom: 0 !important;
    }
    .hover-scale {
        transition: transform 0.2s ease;
    }
    .hover-scale:hover {
        transform: translateY(-2px);
    }
</style>

<script>
    // Config from Server
    const insideCharge = {{ \App\Models\Setting::get('delivery_charge_inside_dhaka', 60) }};
    const outsideCharge = {{ \App\Models\Setting::get('delivery_charge_outside_dhaka', 120) }};
    
    @php
        $hasFree = false;
        if(\App\Models\Setting::get('free_delivery_all')) $hasFree = true;
        $minOrder = \App\Models\Setting::get('free_delivery_over');
        if(!$hasFree && $minOrder && $subtotal >= $minOrder) $hasFree = true;
        if(!$hasFree) {
            $productIds = array_keys($cart);
            $products = \App\Models\Product::whereIn('id', $productIds)->with('category')->get();
            foreach($products as $p) {
                if($p->is_free_delivery || ($p->category && $p->category->is_free_delivery)) {
                    $hasFree = true; break;
                }
            }
        }
    @endphp
    const isFreeShipping = {{ $hasFree ? 'true' : 'false' }};
    const currentTotal = {{ $total }}; 
    
    // Load Divisions from API
    fetch('/api/locations/divisions')
        .then(response => response.json())
        .then(data => {
            const divSelect = document.getElementById('division');
            data.forEach(div => {
                const option = document.createElement('option');
                option.value = div.id;
                option.textContent = div.name + (div.bn_name ? ' (' + div.bn_name + ')' : '');
                divSelect.appendChild(option);
            });
        });

    function loadDistricts() {
        const divId = document.getElementById('division').value;
        const distSelect = document.getElementById('district');
        const upSelect = document.getElementById('upazila');

        distSelect.innerHTML = '<option value="">Select...</option>';
        distSelect.disabled = true;
        upSelect.innerHTML = '<option value="">Select...</option>';
        upSelect.disabled = true;
        
        // Reset Shipping
        calculateShipping();

        if(!divId) return;

        fetch('/api/locations/districts/' + divId)
            .then(res => res.json())
            .then(data => {
                data.forEach(dist => {
                    const option = document.createElement('option');
                    option.value = dist.id;
                    option.textContent = dist.name + (dist.bn_name ? ' (' + dist.bn_name + ')' : '');
                    distSelect.appendChild(option);
                });
                distSelect.disabled = false;
            });
    }

    function loadUpazilas() {
        const distId = document.getElementById('district').value;
        const upSelect = document.getElementById('upazila');

        upSelect.innerHTML = '<option value="">Select...</option>';
        upSelect.disabled = true;
        
        calculateShipping(); // Reset to base (or wait for selection)

        if(!distId) return;

        fetch('/api/locations/upazilas/' + distId)
            .then(res => res.json())
            .then(data => {
                data.forEach(up => {
                    const option = document.createElement('option');
                    option.value = up.id;
                    option.textContent = up.name + (up.bn_name ? ' (' + up.bn_name + ')' : '');
                    // Store Inside Dhaka flag in data attribute
                    if (up.is_inside_dhaka) {
                        option.dataset.inside = "true";
                    } else {
                        option.dataset.inside = "false";
                    }
                    upSelect.appendChild(option);
                });
                upSelect.disabled = false;
            });
    }

    function calculateShipping() {
        const display = document.getElementById('shipping-display');
        const totalDisplay = document.getElementById('final-total');
        const infoBox = document.getElementById('shipping-info-box');
        const infoText = document.getElementById('shipping-info-text');
        const postcodeField = document.querySelector('input[name="postcode"]');
        
        // Check selection
        const upSelect = document.getElementById('upazila');
        const selectedOption = upSelect.options[upSelect.selectedIndex];
        
        // --- Auto Fill Postcode ---
        if (selectedOption && selectedOption.value) {
             fetch('/api/locations/postcode/' + selectedOption.value)
                .then(res => res.json())
                .then(data => {
                    if(data.postCode) {
                        postcodeField.value = data.postCode; 
                        // Flash effect to show it updated
                        postcodeField.classList.add('bg-light');
                        setTimeout(() => postcodeField.classList.remove('bg-light'), 500);
                    }
                });
        }

        if (isFreeShipping) {
             display.innerText = "Free";
             display.classList.add('text-success');
             totalDisplay.innerText = '৳' + currentTotal;
             return;
        }
        
        if (!selectedOption || !selectedOption.value) {
            display.innerText = "...";
            totalDisplay.innerText = '৳' + currentTotal;
            infoBox.classList.add('d-none');
            return;
        }

        const isInside = selectedOption.dataset.inside === "true";
        const charge = isInside ? insideCharge : outsideCharge;
        const details = isInside ? "Inside Dhaka City" : "Outside Dhaka City";

        display.innerText = '৳' + charge;
        display.classList.remove('text-success');
        totalDisplay.innerText = '৳' + (currentTotal + charge);
        
        infoBox.classList.remove('d-none');
        infoText.innerText = `${details} Rate Applied.`;
        
        // Update Payment UI after charge calc
        updatePaymentUI(charge);
    }

    function updatePaymentUI(chargeOverride = null) {
        // Get current charge logic if not passed
        let currentCharge = chargeOverride;
        if (currentCharge === null) {
             const display = document.getElementById('shipping-display');
             const displayText = display.innerText;
             if (displayText.includes('Free')) currentCharge = 0;
             else if (displayText.includes('...')) currentCharge = 0; // Default
             else currentCharge = parseInt(displayText.replace(/[^\d]/g, '')) || 0;
        }

        const method = document.querySelector('input[name="payment_method"]:checked').value;
        const section = document.getElementById('payment-details-section');
        const instruction = document.getElementById('payment-instruction-text');
        const inputs = document.getElementById('trx-inputs');
        const numInput = document.getElementById('payment_number');
        const trxInput = document.getElementById('transaction_id');
        
        // Reset basic visibility
        section.classList.remove('d-none', 'alert-warning', 'alert-info', 'alert-success');
        inputs.classList.add('d-none');
        numInput.removeAttribute('required');
        trxInput.removeAttribute('required');

        const numLabel = document.querySelector('label[for="payment_number"]');

        if (method === 'cod') {
            numLabel.innerHTML = 'Sender Phone Number <span class="text-danger">*</span>';
            // Default styling
            
            if (currentCharge > 0) {
                section.classList.remove('d-none');
                section.classList.add('alert-warning');
                instruction.innerHTML = `
                    <strong>Advance Payment Required:</strong> ৳${currentCharge}<br>
                    Please pay the delivery charge to confirm your order.<br>
                    <strong>bKash/Nagad:</strong> 019XXXXXXXX (Personal)<br>
                    <hr class="my-2">
                    <strong>Bank:</strong> City Bank | <strong>A/C:</strong> 1234567890
                `;
                inputs.classList.remove('d-none');
                numInput.setAttribute('required', 'required');
                trxInput.setAttribute('required', 'required');
            } else {
                section.classList.remove('d-none');
                section.classList.add('alert-success');
                instruction.innerHTML = `<strong>Free Delivery Enabled!</strong> Safe Cash on Delivery. Place order now.`;
                inputs.classList.add('d-none'); 
            }
        } else if (method === 'bkash') {
            numLabel.innerHTML = 'Sender Phone Number <span class="text-danger">*</span>';
            section.classList.remove('d-none');
            section.classList.add('alert-info');
            instruction.innerHTML = `
                <div class="mb-2">Send Money to <strong>bKash Personal: 019XXXXXXXX</strong></div>
                <div>Use Reference: <em>Order</em></div>
            `;
            inputs.classList.remove('d-none');
            numInput.setAttribute('required', 'required');
            trxInput.setAttribute('required', 'required');
        } else if (method === 'bank') {
            numLabel.innerHTML = 'Sender Account Number <span class="text-danger">*</span>';
            section.classList.remove('d-none');
            section.classList.add('alert-info');
            instruction.innerHTML = `
                <div class="mb-2"><strong>Bank:</strong> City Bank | <strong>A/C:</strong> OlexaBD</div>
                <div><strong>A/C No:</strong> 1234567890 | <strong>Branch:</strong> Gulshan</div>
            `;
            inputs.classList.remove('d-none');
            numInput.setAttribute('required', 'required');
            trxInput.setAttribute('required', 'required');
        }
    }
</script>
@endsection
