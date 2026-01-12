@extends('layouts.admin')

@section('header', 'Orders')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" name="order_id" class="form-control" placeholder="Search by Order Code" value="{{ request('order_id') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Payment</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-filter"></i> Filter</button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-light"><i class="bi bi-x-circle"></i></a>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Order Code</th>
                        <th>Marketing Source</th>
                        <th>System Portal</th>
                        <th>Device</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Last Updated</th>
                        <th>Updated By</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td class="ps-4 text-muted small">#{{ $order->id }}</td>
                        <td>
                            <div class="fw-bold font-monospace text-primary h6 mb-1">{{ $order->order_code }}</div>
                            <small class="text-muted">{{ $order->created_at->format('M d, H:i') }}</small>
                        </td>
                        <td>
                            {{-- Logic: Show Traffic Source (Campaign) if exists, OR Source (Platform) if it's marketing-related --}}
                            @if($order->traffic_source)
                                <span class="badge bg-success" style="font-size: 0.8em; text-transform: uppercase;">
                                    <i class="bi bi-megaphone-fill me-1"></i> {{ $order->traffic_source }}
                                </span>
                            @elseif($order->source && $order->source !== 'web' && $order->source !== 'app')
                                <span class="badge bg-primary bg-opacity-75" style="font-size: 0.8em; text-transform: uppercase;">
                                    {{ $order->source }}
                                </span>
                            @else
                                <span class="badge bg-light text-secondary border" style="font-size: 0.7em;">Direct / Organic</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 0.7em;">
                                {{ $order->order_portal ?? 'Unknown' }}
                            </span>
                        </td>
                        <td>
                            {{-- Device/Platform Column --}}
                            @if($order->source === 'app' || $order->source === 'android' || $order->source === 'ios')
                                <span class="badge bg-info text-dark" style="font-size: 0.7em;">APP</span>
                            @elseif($order->source === 'web' || empty($order->source))
                                <span class="badge bg-light text-dark border" style="font-size: 0.7em;">WEB</span>
                            @else
                                {{-- It's a specific platform like Facebook, displayed as WEB since it's likely a browser view --}}
                                <span class="badge bg-light text-dark border" style="font-size: 0.7em;">WEB ({{ ucfirst($order->source) }})</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold">{{ $order->user->name ?? 'Guest' }}</div>
                        </td>
                        <td>
                            ৳{{ $order->total_amount }}
                        </td>
                        <td>
                            <span class="small text-muted">{{ $order->updated_at->format('M d, Y h:i A') }}</span>
                        </td>
                        <td>
                            @if($order->updated_by)
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                    <i class="bi bi-person-circle me-1"></i> {{ $order->updater?->name ?? 'Admin' }}
                                </span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if($order->status == 'completed')
                                <span class="badge bg-success-subtle text-success">Completed</span>
                            @elseif($order->status == 'pending')
                                <span class="badge bg-warning-subtle text-warning text-dark">Pending</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">{{ ucfirst($order->status) }}</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View/Manage</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-3 py-3">
            {{ $orders->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
