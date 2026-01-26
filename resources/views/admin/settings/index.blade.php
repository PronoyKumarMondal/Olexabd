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

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill">
                        Save Settings
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
