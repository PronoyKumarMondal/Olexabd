@extends('layouts.app')

@section('content')
<div class="bg-light py-5">
    <div class="container">
        <h2 class="fw-bold mb-4">Checkout</h2>

        <div class="row g-4">
            <!-- Left Side: Form -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Shipping Information</h5>
                        <form action="{{ route('checkout.place') }}" method="POST" id="checkoutForm">
                            @csrf
                            <input type="hidden" name="mode" value="{{ $mode }}">
                            
                            <!-- Name & Phone -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone" class="form-control" placeholder="01XXXXXXXXX" pattern="(01)[0-9]{9}" required>
                                </div>
                            </div>
                            
                            <!-- Location Dropdowns -->
                            <div class="row g-3 mb-3"> <!-- Added g-3 for consistent gap -->
                                <div class="col-12 col-md-4"> <!-- Ensure full width on mobile -->
                                    <label class="form-label">Division <span class="text-danger">*</span></label>
                                    <select name="division" id="division" class="form-select w-100" required onchange="loadDistricts()">
                                        <option value="">Select Division</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label">District <span class="text-danger">*</span></label>
                                    <select name="district" id="district" class="form-select w-100" required disabled onchange="loadUpazilas()">
                                        <option value="">Select District</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Upazila / Area <span class="text-danger">*</span></label>
                                    <select name="upazila" id="upazila" class="form-select w-100" required disabled onchange="calculateShipping()">
                                        <option value="">Select Area</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Postcode & Detailed Address -->
                            <!-- Postcode & Detailed Address -->
                             <div class="mb-3">
                                <label class="form-label">Post Code <span class="text-danger">*</span></label>
                                <input type="text" name="postcode" class="form-control" placeholder="e.g. 1230" required>
                             </div>

                            <div class="mb-4">
                                <label class="form-label">Detailed Address <span class="text-danger">*</span></label>
                                <textarea name="address" class="form-control" rows="3" placeholder="House #, Road #, Block/Sector, Ward No, etc." required></textarea>
                            </div>

                            <!-- Payment Method Section -->
                            <hr class="my-4">
                            <h5 class="fw-bold mb-3">Payment Method</h5>
                            
                            <div class="mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="method_cod" value="cod" checked onchange="updatePaymentUI()">
                                    <label class="form-check-label fw-bold" for="method_cod">
                                        Cash on Delivery (COD)
                                    </label>
                                    <div class="small text-muted ms-2">Pay delivery charge in advance if applicable.</div>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="method_bkash" value="bkash" onchange="updatePaymentUI()">
                                    <label class="form-check-label fw-bold" for="method_bkash">
                                        bKash Personal
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="method_bank" value="bank" onchange="updatePaymentUI()">
                                    <label class="form-check-label fw-bold" for="method_bank">
                                        Bank Transfer
                                    </label>
                                </div>
                            </div>

                            <!-- Payment Instructions & Inputs -->
                            <div id="payment-details-section" class="alert alert-light border p-3 rounded-3 d-none">
                                <div id="payment-instruction-text" class="mb-3">
                                    <!-- Dynamic Text -->
                                </div>
                                
                                <div id="trx-inputs" class="d-none">
                                    <div class="mb-3">
                                        <label class="form-label" for="payment_number">Sender Phone Number <span class="text-danger">*</span></label>
                                        <input type="text" name="payment_number" id="payment_number" class="form-control" placeholder="01XXXXXXXXX">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Transaction ID (TrxID) <span class="text-danger">*</span></label>
                                        <input type="text" name="transaction_id" id="transaction_id" class="form-control" placeholder="e.g. 8J7S6D5F">
                                    </div>
                                    <small class="text-muted">Admin will verify this information before confirming order.</small>
                                </div>
                            </div>


                            <button type="submit" class="btn btn-dark w-100 rounded-pill py-3 fw-bold shadow-lg">
                                Place Order
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Side: Order Summary -->
            <!-- ... (Summary Section remains mostly same, just shipping display logic) ... -->
            <div class="col-lg-4">
                 <!-- ... -->
                 <div class="card border-0 shadow-sm rounded-4 position-sticky" style="top: 100px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Order Summary</h5>
                        <div class="mb-3" style="max-height: 200px; overflow-y: auto;">
                            @foreach($cart as $item)
                                <div class="d-flex align-items-center mb-2">
                                    <div class="ms-2 flex-grow-1">
                                        <small class="d-block fw-bold text-truncate" style="max-width: 150px;">{{ $item['name'] }}</small>
                                        <small class="text-muted">x{{ $item['quantity'] }}</small>
                                    </div>
                                    <small class="fw-bold">৳{{ $item['price'] * $item['quantity'] }}</small>
                                </div>
                            @endforeach
                        </div>
                        <hr>
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
                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-muted">Shipping</span>
                            <span class="fw-bold" id="shipping-display">...</span>
                        </div>
                         <div class="alert alert-info py-2 small mb-3 d-none" id="shipping-info-box">
                            <i class="bi bi-info-circle me-1"></i> <span id="shipping-info-text"></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="h5 fw-bold mb-0">Total</span>
                            <span class="h5 fw-bold mb-0 text-primary" id="final-total">৳{{ max(0, $subtotal - $discount) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

        distSelect.innerHTML = '<option value="">Select District</option>';
        distSelect.disabled = true;
        upSelect.innerHTML = '<option value="">Select Area</option>';
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

        upSelect.innerHTML = '<option value="">Select Area</option>';
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
             // Parse from display text logic or global logic
             // Simpler: Recalculate or use global var if available. 
             // We'll rely on global recalculation call mostly.
             // But let's check the display text if it's "Free" or a number
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
        const total = currentTotal + currentCharge;

        section.classList.remove('d-none', 'alert-warning', 'alert-info', 'alert-success');
        inputs.classList.add('d-none');
        numInput.removeAttribute('required');
        trxInput.removeAttribute('required');

        const numLabel = document.querySelector('label[for="payment_number"]');

        if (method === 'cod') {
            numLabel.innerHTML = 'Sender Phone Number <span class="text-danger">*</span>';
             // ... existing cod logic ...
            if (currentCharge > 0) {
                section.classList.remove('d-none');
                section.classList.add('alert-warning');
                instruction.innerHTML = `
                    <strong>Advance Payment Required:</strong> ৳${currentCharge}<br>
                    Please pay the delivery charge to confirm your order.<br>
                    <strong>bKash/Nagad:</strong> 019XXXXXXXX (Personal)<br>
                    <hr class="my-2">
                    <strong>Bank:</strong> City Bank | <strong>A/C:</strong> 1234567890<br>
                    <strong>Branch:</strong> Gulshan | <strong>Routing:</strong> 123456789
                `;
                inputs.classList.remove('d-none');
                numInput.setAttribute('required', 'required');
                trxInput.setAttribute('required', 'required');
            } else {
                section.classList.remove('d-none');
                section.classList.add('alert-success');
                instruction.innerHTML = `<strong>Free Delivery Enabled!</strong> No advance payment required. Just place the order.`;
                inputs.classList.add('d-none'); // Hide inputs for free COD
            }
        } else if (method === 'bkash') {
            numLabel.innerHTML = 'Sender Phone Number <span class="text-danger">*</span>';
            section.classList.remove('d-none');
            section.classList.add('alert-info');
            instruction.innerHTML = `
                <strong>Total Amount:</strong> ৳${total}<br>
                Send Money to <strong>bKash Personal: 019XXXXXXXX</strong><br>
                Use Reference: <em>Order</em>
            `;
            inputs.classList.remove('d-none');
            numInput.setAttribute('required', 'required');
            trxInput.setAttribute('required', 'required');
        } else if (method === 'bank') {
            numLabel.innerHTML = 'Sender Account Number <span class="text-danger">*</span>';
            section.classList.remove('d-none');
            section.classList.add('alert-info');
            instruction.innerHTML = `
                <strong>Total Amount:</strong> ৳${total}<br>
                <strong>Bank:</strong> City Bank<br>
                <strong>A/C Name:</strong> OlexaBD<br>
                <strong>A/C No:</strong> 1234567890<br>
                <strong>Branch:</strong> Gulshan<br>
                <strong>Routing:</strong> 123456789
            `;
            inputs.classList.remove('d-none');
            numInput.setAttribute('required', 'required');
            trxInput.setAttribute('required', 'required');
        }
    }
</script>
@endsection
