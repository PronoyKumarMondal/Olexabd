@extends('layouts.admin')

@section('title', 'Create Order')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create New Order</h1>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.orders.store') }}" method="POST" id="createOrderForm">
        @csrf
        <div class="row">
            <!-- Customer & Channel Details -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Customer & Source</h6>
                    </div>
                    <div class="card-body">
                        <!-- Customer -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Customer <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-select select2" required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('user_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} ({{ $customer->phone ?? $customer->email }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted"><a href="{{ route('admin.customers.index') }}" target="_blank">Add new customer</a> if not exists.</small>
                        </div>

                        <!-- Channel -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Sales Channel / Source</label>
                            <select name="channel_id" class="form-select">
                                <option value="">Web (Direct/Default)</option>
                                @foreach($channels as $channel)
                                    <option value="{{ $channel->id }}" {{ old('channel_id') == $channel->id ? 'selected' : '' }}>
                                        {{ $channel->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Payment & Shipping -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">Payment & Shipping</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Payment Method</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="cod" selected>Cash on Delivery</option>
                                <option value="bkash">bKash</option>
                                <option value="nagad">Nagad</option>
                                <option value="bank">Bank Transfer</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Payment Status</label>
                            <select name="payment_status" class="form-select" required>
                                <option value="unpaid" selected>Unpaid</option>
                                <option value="paid">Paid</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Shipping Address <span class="text-danger">*</span></label>
                            <textarea name="shipping_address" class="form-control" rows="3" required placeholder="Full address">{{ old('shipping_address') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Order Items</h6>
                        <button type="button" class="btn btn-sm btn-success" onclick="addItem()">
                            <i class="bi bi-plus-lg"></i> Add Item
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th width="120">Price</th>
                                        <th width="100">Qty</th>
                                        <th width="120">Subtotal</th>
                                        <th width="50"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsContainer">
                                    <!-- Items loop or JS added items -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total Amount:</td>
                                        <td colspan="2" class="fw-bold">Login to see</td>
                                        <td class="d-none"></td> <!-- Hidden for alignment validation -->
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary btn-lg px-5">Create Order</button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Template for JS Item -->
<script>
    const products = @json($products);

    function addItem() {
        const index = document.querySelectorAll('.item-row').length;
        const row = `
            <tr class="item-row" id="row_${index}">
                <td>
                    <select name="products[${index}][id]" class="form-select product-select" onchange="updatePrice(${index})" required>
                        <option value="">Select Product...</option>
                        ${products.map(p => `<option value="${p.id}" data-price="${p.price}" ${p.stock <= 0 ? 'disabled' : ''}>${p.name} (Code: ${p.code}) - Stock: ${p.stock}</option>`).join('')}
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control price-input" id="price_${index}" readonly>
                </td>
                <td>
                    <input type="number" name="products[${index}][quantity]" class="form-control qty-input" value="1" min="1" onchange="updateSubtotal(${index})" required>
                </td>
                <td>
                    <input type="text" class="form-control subtotal-input" id="subtotal_${index}" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        document.getElementById('itemsContainer').insertAdjacentHTML('beforeend', row);
    }

    function updatePrice(index) {
        const select = document.querySelector(`#row_${index} .product-select`);
        const priceInput = document.getElementById(`price_${index}`);
        const option = select.options[select.selectedIndex];
        const price = option.getAttribute('data-price');
        
        priceInput.value = price;
        updateSubtotal(index);
    }

    function updateSubtotal(index) {
        const price = parseFloat(document.getElementById(`price_${index}`).value) || 0;
        const qty = parseInt(document.querySelector(`#row_${index} .qty-input`).value) || 1;
        const subtotal = price * qty;
        
        document.getElementById(`subtotal_${index}`).value = subtotal;
        calculateTotal();
    }

    function removeRow(index) {
        document.getElementById(`row_${index}`).remove();
        calculateTotal();
    }

    function calculateTotal() {
        // Simple sum logic here if needed for display
    }

    // Add one empty row on load
    document.addEventListener('DOMContentLoaded', () => {
        addItem();
    });
</script>
@endsection
