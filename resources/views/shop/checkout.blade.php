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
    }
</script>
@endsection
