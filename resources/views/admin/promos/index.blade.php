@extends('layouts.admin')

@section('header', 'Promo Codes')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('admin.promos.create') }}" class="btn btn-primary">Add Promo Code</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Code</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Min. Order</th>
                        <th>Expiry</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($promos as $promo)
                    <tr>
                        <td class="ps-4"><span class="font-monospace fw-bold text-primary">{{ $promo->code }}</span></td>
                        <td><span class="badge {{ $promo->type == 'percent' ? 'bg-info' : 'bg-primary' }}">{{ ucfirst($promo->type) }}</span></td>
                        <td class="fw-bold">{{ $promo->type == 'fixed' ? '৳' : '' }}{{ $promo->value }}{{ $promo->type == 'percent' ? '%' : '' }}</td>
                        <td>{{ $promo->min_order_amount ? '৳'.$promo->min_order_amount : '-' }}</td>
                        <td>
                            @if($promo->expires_at)
                                <span class="{{ $promo->expires_at->isPast() ? 'text-danger' : '' }}">{{ $promo->expires_at->format('M d, Y') }}</span>
                            @else
                                <span class="text-muted">Never</span>
                            @endif
                        </td>
                        <td>
                            @if($promo->is_active)
                                <span class="badge bg-success-subtle text-success">Active</span>
                            @else
                                <span class="badge bg-light text-dark border">Inactive</span>
                            @endif
                        </td>
                         <td>
                            <div class="small text-muted" style="font-size: 0.7em;">
                                <div><i class="bi bi-clock"></i> {{ $promo->updated_at->format('M d, H:i') }}</div>
                                @if($promo->updated_by)
                                <div><i class="bi bi-pencil"></i> {{ $promo->updater->name ?? 'Admin' }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.promos.edit', $promo) }}" class="btn btn-sm btn-outline-primary me-1">Edit</a>
                            <form action="{{ route('admin.promos.destroy', $promo) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
