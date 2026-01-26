@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-warning text-dark text-center py-3">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-envelope-paper me-2"></i>Verify Your Email</h4>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="mb-4">
                        <i class="bi bi-envelope-check text-warning" style="font-size: 4rem;"></i>
                    </div>

                    <p class="mb-4 text-muted">
                        Thanks for signing up! Before getting started, you must verify your email address.
                        <br><br>
                        <strong>Note:</strong> You can request a new link once every 5 minutes.
                    </p>

                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success mb-4" role="alert">
                            A new verification link has been sent to the email address you provided during registration.
                        </div>
                    @endif

                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                Resend Verification Email
                            </button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
