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
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" name="phone" class="form-control" placeholder="01XXXXXXXXX" pattern="(01)[0-9]{9}" required>
                                </div>
                            </div>
                            
                            <!-- Location Dropdowns -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Division</label>
                                    <select name="division" id="division" class="form-select" required onchange="loadDistricts()">
                                        <option value="">Select Division</option>
                                        <!-- Populated via JS -->
                                    </select>
                                    <input type="hidden" name="division_name" id="division_name">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">District</label>
                                    <select name="district" id="district" class="form-select" required disabled onchange="loadUpazilas()">
                                        <option value="">Select District</option>
                                    </select>
                                    <input type="hidden" name="district_name" id="district_name">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Upazila / Area</label>
                                    <select name="upazila" id="upazila" class="form-select" required disabled onchange="updateUpazilaName()">
                                        <option value="">Select Area</option>
                                    </select>
                                    <input type="hidden" name="upazila_name" id="upazila_name">
                                </div>
                            </div>

                            <!-- Detailed Address -->
                            <div class="mb-4">
                                <label class="form-label">Detailed Address (House, Road, etc.)</label>
                                <textarea name="address" class="form-control" rows="3" placeholder="Optional. House #, Road #, etc."></textarea>
                            </div>

                            <button type="submit" class="btn btn-dark w-100 rounded-pill py-3 fw-bold shadow-lg">
                                Place Order
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Side: Order Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 position-sticky" style="top: 100px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Order Summary</h5>
                        
                        <!-- Items Preview -->
                        <div class="mb-3" style="max-height: 200px; overflow-y: auto;">
                            @foreach($cart as $item)
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-light rounded text-center d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-box small text-muted"></i>
                                    </div>
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
                            <i class="bi bi-info-circle me-1"></i> <span id="shipping-info-text">Please select a district.</span>
                        </div>

                        <hr class="border-secondary opacity-25">
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
    let locations = [];
    
    // Config from Server
    const insideCharge = {{ \App\Models\Setting::get('delivery_charge_inside_dhaka', 60) }};
    const outsideCharge = {{ \App\Models\Setting::get('delivery_charge_outside_dhaka', 120) }};
    // Global Free Shipping Checks
    // We already calculated subtotal on server, but we need to know if it qualifies for free shipping BEFORE location check.
    // Actually, simple way: Check if any item in cart is free, or if global free is on. 
    // We can pass a bool from controller.
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
    const currentTotal = {{ $total }}; // This includes discount but excludes shipping
    
    // Load JSON
    fetch('{{ asset("assets/data/bd_locations.json") }}')
        .then(response => response.json())
        .then(data => {
            locations = data;
            populateDivisions();
        });

    function populateDivisions() {
        const divSelect = document.getElementById('division');
        locations.forEach(div => {
            const option = document.createElement('option');
            option.value = div.id;
            option.textContent = div.name + ' (' + div.bn_name + ')';
            divSelect.appendChild(option);
        });
    }

    function loadDistricts() {
        const divId = document.getElementById('division').value;
        const distSelect = document.getElementById('district');
        const upSelect = document.getElementById('upazila');
        
        // Update Hidden Name
        const divElement = document.getElementById('division');
        document.getElementById('division_name').value = divElement.options[divElement.selectedIndex].text;

        distSelect.innerHTML = '<option value="">Select District</option>';
        distSelect.disabled = true;
        upSelect.innerHTML = '<option value="">Select Area</option>';
        upSelect.disabled = true;

        if(!divId) return;

        const division = locations.find(d => d.id == divId);
        if(division && division.districts) {
            division.districts.forEach(dist => {
                const option = document.createElement('option');
                option.value = dist.id;
                option.textContent = dist.name + ' (' + dist.bn_name + ')';
                distSelect.appendChild(option);
            });
            distSelect.disabled = false;
        }
        calculateShipping(''); // Reset
    }

    function loadUpazilas() {
        const divId = document.getElementById('division').value;
        const distId = document.getElementById('district').value;
        const upSelect = document.getElementById('upazila');
        
        // Update Hidden Name
        const distElement = document.getElementById('district');
        const distName = distElement.options[distElement.selectedIndex].text;
        document.getElementById('district_name').value = distName;

        upSelect.innerHTML = '<option value="">Select Area</option>';
        upSelect.disabled = true;

        if(!divId || !distId) {
             calculateShipping('');
             return;
        }

        calculateShipping(distId, distName);

        const division = locations.find(d => d.id == divId);
        const district = division.districts.find(d => d.id == distId);
        
        if(district && district.upazilas) {
            district.upazilas.forEach(up => {
                const option = document.createElement('option');
                option.value = up.id;
                option.textContent = up.name + ' (' + up.bn_name + ')';
                upSelect.appendChild(option);
            });
            upSelect.disabled = false;
        }
    }

    function updateUpazilaName() {
        const upElement = document.getElementById('upazila');
        document.getElementById('upazila_name').value = upElement.options[upElement.selectedIndex].text;
    }

    function calculateShipping(distId, distName = "") {
        const display = document.getElementById('shipping-display');
        const totalDisplay = document.getElementById('final-total');
        const infoBox = document.getElementById('shipping-info-box');
        const infoText = document.getElementById('shipping-info-text');

        if (isFreeShipping) {
            display.innerText = "Free";
            display.classList.add('text-success');
            totalDisplay.innerText = '৳' + currentTotal;
            infoBox.classList.remove('d-none');
            infoText.innerText = "Free Delivery Applied!";
            return;
        }

        if (!distId) {
            display.innerText = "...";
            totalDisplay.innerText = '৳' + currentTotal;
            infoBox.classList.add('d-none');
            return;
        }

        // Logic: Dhaka ID is 1 (based on JSON check) or Name contains Dhaka
        let charge = outsideCharge;
        let details = "Outside Dhaka";
        
        // Robust check
        if (distId == '1' || distName.toLowerCase().includes('dhaka')) {
            charge = insideCharge;
            details = "Inside Dhaka";
        }

        display.innerText = '৳' + charge;
        display.classList.remove('text-success');
        
        totalDisplay.innerText = '৳' + (currentTotal + charge);
        
        infoBox.classList.remove('d-none');
        infoText.innerText = `${details} Rate Applied.`;
    }
</script>
@endsection
