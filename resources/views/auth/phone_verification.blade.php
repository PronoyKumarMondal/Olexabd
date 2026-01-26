@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-warning text-dark text-center py-3">
                    <h4 class="mb-0 fw-bold">One Last Step! 📱</h4>
                </div>
                <div class="card-body p-4">
                    <p class="text-center text-muted mb-4">
                        Welcome, <strong>{{ Auth::user()->name }}</strong>!<br>
                        Please provide your mobile number required for order delivery.
                    </p>

                    <form method="POST" action="{{ route('auth.phone.save') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" required placeholder="01XXXXXXXXX" autofocus>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Address (Optional for now)</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="You can add address now or later"></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning fw-bold btn-lg">Save & Continue</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
