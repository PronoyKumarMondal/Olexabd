@extends('layouts.admin')

@section('header', 'Search History')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Query</th>
                        <th>User</th>
                        <th>Results Found</th>
                        <th>IP Address</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($searches as $search)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">{{ $search->query }}</td>
                        <td>
                            @if($search->user)
                                <div class="fw-bold">{{ $search->user->name }}</div>
                                <small class="text-muted">{{ $search->user->email }}</small>
                            @else
                                <span class="badge bg-light text-dark border">Guest</span>
                            @endif
                        </td>
                        <td>
                            @if($search->results_count == 0)
                                <span class="badge bg-danger bg-opacity-10 text-danger">0 Results</span>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success">{{ $search->results_count }} Results</span>
                            @endif
                        </td>
                        <td class="font-monospace small text-muted">{{ $search->ip_address ?? '-' }}</td>
                        <td class="text-muted small">
                            {{ $search->updated_at->format('M d, Y h:i A') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">No search history found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-3 py-3">
            {{ $searches->links() }}
        </div>
    </div>
</div>
@endsection
