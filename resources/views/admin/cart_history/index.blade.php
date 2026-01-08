@extends('layouts.admin')

@section('header', 'Cart History')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('admin.cart_history.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label small fw-bold text-muted">Search</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Customer or Product...">
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label small fw-bold text-muted">Status</label>
                <select class="form-select" name="status">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                    <option value="removed" {{ request('status') == 'removed' ? 'selected' : '' }}>Removed</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="start_date" class="form-label small fw-bold text-muted">From Date</label>
                <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-2">
                <label for="end_date" class="form-label small fw-bold text-muted">To Date</label>
                <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filter</button>
                <a href="{{ route('admin.cart_history.index') }}" class="btn btn-light border"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">User</th>
                        <th>Product</th>
                        <th>Status</th>
                        <th>Qty</th>
                        <th>Price Snapshot</th>
                        <th>Total</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold">{{ $item->user->name ?? 'Unknown' }}</div>
                            <small class="text-muted">{{ $item->user->email ?? '-' }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($item->product && $item->product->image)
                                    <img src="{{ $item->product->image }}" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                @endif
                                <div>
                                    <div class="fw-bold">{{ $item->product->name ?? 'Deleted Product' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($item->status == 'active')
                                <span class="badge bg-success bg-opacity-10 text-success">Active</span>
                            @elseif($item->status == 'sold')
                                <span class="badge bg-primary bg-opacity-10 text-primary">Sold</span>
                            @elseif($item->status == 'removed')
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">Removed</span>
                            @else
                                <span class="badge bg-light text-dark border">{{ $item->status }}</span>
                            @endif
                        </td>
                        <td class="fw-bold">{{ $item->quantity }}</td>
                        <td>৳{{ $item->price }}</td>
                        <td class="fw-bold">৳{{ $item->price * $item->quantity }}</td>
                        <td class="text-muted small">
                            {{ $item->updated_at->format('M d, Y h:i A') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No cart history found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-3 py-3">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection
