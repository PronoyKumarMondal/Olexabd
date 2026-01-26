@extends('layouts.admin')

@section('header', 'System Settings')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Delivery Charges</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Inside Dhaka (BDT)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">৳</span>
                            <input type="number" name="delivery_charge_inside_dhaka" 
                                class="form-control form-control-lg border-start-0 ps-0" 
                                value="{{ \App\Models\Setting::get('delivery_charge_inside_dhaka', 60) }}" 
                                required min="0">
                        </div>
                        <div class="form-text">Standard delivery charge for Dhaka city area.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Outside Dhaka (BDT)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">৳</span>
                            <input type="number" name="delivery_charge_outside_dhaka" 
                                class="form-control form-control-lg border-start-0 ps-0" 
                                value="{{ \App\Models\Setting::get('delivery_charge_outside_dhaka', 120) }}" 
                                required min="0">
                        </div>
                        <div class="form-text">Delivery charge for all other districts.</div>
                    </div>

                    <hr class="my-4">
                    
                    <h5 class="mb-3 fw-bold text-dark">Free Delivery Rules</h5>
                    
                    <div class="card bg-light border-0 p-3 mb-4">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="free_delivery_all" id="freeAll" 
                                value="1" {{ \App\Models\Setting::get('free_delivery_all') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="freeAll">Enable Free Delivery for Everyone</label>
                            <div class="form-text mt-0">Overrides all other delivery charges.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Free Delivery for Orders Over (Amount)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">৳</span>
                                <input type="number" name="free_delivery_over" 
                                    class="form-control border-start-0 ps-0" 
                                    placeholder="e.g. 5000"
                                    value="{{ \App\Models\Setting::get('free_delivery_over') }}">
                            </div>
                            <div class="form-text">Leave empty to disable this rule.</div>
                        </div>
                    </div>

                    <div class="alert alert-info border-0 d-flex align-items-center mb-0">
                        <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                        <div>
                            <strong>Specific Products or Categories?</strong><br>
                            You can mark specific items as "Free Delivery" directly in the 
                            <a href="{{ route('admin.products.index') }}" class="alert-link">Products</a> or 
                            <a href="{{ route('admin.categories.index') }}" class="alert-link">Categories</a> management pages.
                        </div>
                    </div>

                    <hr class="my-4">

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill">
                        Save Settings
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
