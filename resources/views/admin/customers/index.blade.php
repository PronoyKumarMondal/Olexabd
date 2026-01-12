@extends('layouts.admin')

@section('header', 'Customers')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <form action="{{ route('admin.customers.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-2">
                <input type="text" name="id" class="form-control" placeholder="ID" value="{{ request('id') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Name, Email or Phone" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="source" class="form-select">
                    <option value="">All Sources</option>
                    <option value="web" {{ request('source') === 'web' ? 'selected' : '' }}>Web</option>
                    <option value="app" {{ request('source') === 'app' ? 'selected' : '' }}>Mobile App</option>
                    <option value="fb_app" {{ request('source') === 'fb_app' ? 'selected' : '' }}>Facebook</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Search</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.customers.create') }}" class="btn btn-success w-100">
                    <i class="bi bi-person-plus-fill"></i> Add New
                </a>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Source</th>
                        <th>Orders</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                    <tr>
                        <td class="ps-4 text-muted small">#{{ $customer->id }}</td>
                        <td class="fw-bold">{{ $customer->name }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>
                            @if($customer->source === 'app')
                                <span class="badge bg-info text-dark">APP</span>
                            @elseif($customer->source === 'fb_app')
                                <span class="badge bg-primary">FB</span>
                            @else
                                <span class="badge bg-light text-dark border">WEB</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $customer->orders->count() }} Orders</span>
                        </td>
                        <td class="text-muted">{{ $customer->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $customers->links() }}
        </div>
    </div>
</div>
@endsection
