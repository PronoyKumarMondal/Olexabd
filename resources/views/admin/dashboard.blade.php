@extends('layouts.admin')

@section('header')
<div class="d-flex justify-content-between align-items-center w-100">
    <span>Dashboard</span>
    <form method="GET" action="{{ route('admin.dashboard') }}" class="d-flex align-items-center gap-2">
        <label class="small fw-bold text-muted mb-0">Period:</label>
        <input type="month" name="month" class="form-control form-control-sm" value="{{ $filterMonth }}" onchange="this.form.submit()">
    </form>
</div>
@endsection

@section('content')
<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card p-3 h-100 border-0 border-start border-primary border-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="text-muted small text-uppercase fw-bold ls-1">Revenue ({{ \Carbon\Carbon::parse($filterMonth)->format('M') }})</div>
                <div class="bg-primary-subtle text-primary p-2 rounded-circle">
                    <i class="bi bi-currency-dollar fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-0">৳{{ number_format($totalRevenue, 2) }}</h3>
            <small class="text-secondary">Sales this month</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 h-100 border-0 border-start border-info border-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="text-muted small text-uppercase fw-bold ls-1">Orders ({{ \Carbon\Carbon::parse($filterMonth)->format('M') }})</div>
                <div class="bg-info-subtle text-info p-2 rounded-circle">
                    <i class="bi bi-cart fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-0">{{ $totalOrders }}</h3>
            <small class="text-muted">Orders this month</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 h-100 border-0 border-start border-warning border-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="text-muted small text-uppercase fw-bold ls-1">Products</div>
                <div class="bg-warning-subtle text-warning p-2 rounded-circle">
                    <i class="bi bi-box-seam fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-0">{{ $totalProducts }}</h3>
            <small class="text-muted">{{ count($lowStockProducts) }} Low Stock</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 h-100 border-0 border-start border-success border-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="text-muted small text-uppercase fw-bold ls-1">New Customers</div>
                <div class="bg-success-subtle text-success p-2 rounded-circle">
                    <i class="bi bi-people fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-0">{{ $totalCustomers }}</h3>
            <small class="text-success">Joined this month</small>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Daily Sales Analytics ({{ \Carbon\Carbon::parse($filterMonth)->format('F Y') }})</h6>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Order Status Distribution</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center position-relative">
                 @if(count($statusData) > 0)
                    <canvas id="orderStatusChart" style="max-height: 250px;"></canvas>
                @else
                    <div class="text-center text-muted">
                        <i class="bi bi-clipboard-x fs-1 opacity-50"></i>
                        <p class="mt-2 text-sm">No orders this period</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders & Low Stock -->
<div class="row g-4">
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Recent Orders (All Time)</h6>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Code</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                        <tr>
                            <td class="ps-4 text-primary font-monospace small">#{{ $order->order_code }}</td>
                            <td class="fw-bold">{{ $order->user->name ?? 'Guest' }}</td>
                            <td>৳{{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                @if($order->status == 'completed')
                                    <span class="badge bg-success-subtle text-success">Done</span>
                                @elseif($order->status == 'pending')
                                    <span class="badge bg-warning-subtle text-warning text-dark">Pending</span>
                                @else
                                    <span class="badge bg-light text-dark border">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $order->created_at->format('M d') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-danger">Low Stock Alert</h6>
                <a href="{{ route('admin.products.index', ['status' => 'low_stock']) }}" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="list-group list-group-flush">
                @forelse($lowStockProducts as $product)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                             @if($product->image)
                                <img src="{{ $product->image }}" class="rounded" width="32" height="32" style="object-fit:cover;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="bi bi-box"></i>
                                </div>
                            @endif
                            <div>
                                <div class="fw-bold small">{{ Str::limit($product->name, 20) }}</div>
                                <div class="text-muted x-small font-monospace">{{ $product->code }}</div>
                            </div>
                        </div>
                        <span class="badge bg-danger rounded-pill">{{ $product->stock }} left</span>
                    </div>
                @empty
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-check-circle fs-4 mb-2 d-block text-success"></i>
                        No low stock items.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sales Chart
        new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels: @json($chartLabels).map(day => 'Day ' + day),
                datasets: [{
                    label: 'Revenue (৳)',
                    data: @json($chartData),
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: (c) => '৳' + c.raw } } },
                scales: { 
                    y: { beginAtZero: true, grid: { borderDash: [2, 4] } }, 
                    x: { grid: { display: false } } 
                }
            }
        });

        // Order Status Chart
        @if(count($statusData) > 0)
        const statusData = @json($statusData);
        const total = statusData.reduce((a, b) => a + b, 0);

        new Chart(document.getElementById('orderStatusChart'), {
            type: 'doughnut',
            data: {
                labels: @json($statusLabels),
                datasets: [{
                    data: statusData,
                    backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444', '#64748b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                cutout: '70%',
                plugins: { 
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let value = context.raw;
                                let percentage = ((value / total) * 100).toFixed(1) + '%';
                                return context.label + ': ' + value + ' (' + percentage + ')';
                            }
                        }
                    }
                }
            }
        });
        @endif
    });
</script>
@endsection
