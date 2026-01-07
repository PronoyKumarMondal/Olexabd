@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h2 class="fw-bold mb-4">My Profile</h2>

            <!-- Quick Links -->
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-primary w-100 rounded-pill py-2">
                        <i class="bi bi-box-seam me-2"></i> My Orders
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('orders.track') }}" class="btn btn-outline-secondary w-100 rounded-pill py-2">
                        <i class="bi bi-truck me-2"></i> Track Order
                    </a>
                </div>
            </div>

            <!-- Profile Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Profile Information</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            
                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="mt-2 text-warning">
                                    Your email address is unverified.
                                    <button form="send-verification" class="btn btn-link p-0">Click here to re-send the verification email.</button>
                                </div>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary rounded-pill px-4">Save Changes</button>

                        @if (session('status') === 'profile-updated')
                            <span class="ms-2 text-success small"><i class="bi bi-check-circle"></i> Saved.</span>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Password Update -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Update Password</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control">
                            @error('current_password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control">
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary rounded-pill px-4">Update Password</button>

                        @if (session('status') === 'password-updated')
                            <span class="ms-2 text-success small"><i class="bi bi-check-circle"></i> Saved.</span>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Logout -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold">
                            <i class="bi bi-box-arrow-right me-2"></i> Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
