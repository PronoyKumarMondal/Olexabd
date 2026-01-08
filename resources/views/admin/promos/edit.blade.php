@extends('layouts.admin')

@section('header', 'Edit Promo Code')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.promos.update', $promo) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Promo Code</label>
                            <input type="text" name="code" class="form-control text-uppercase" value="{{ $promo->code }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Active Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" {{ $promo->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="isActive">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select" id="typeSelect">
                                <option value="fixed" {{ $promo->type == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                <option value="percent" {{ $promo->type == 'percent' ? 'selected' : '' }}>Percentage (%)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Value</label>
                            <div class="input-group">
                                <span class="input-group-text {{ $promo->type == 'percent' ? 'd-none' : '' }}" id="valuePrefix">৳</span>
                                <input type="number" step="0.01" name="value" class="form-control" value="{{ $promo->value }}" required>
                                <span class="input-group-text {{ $promo->type == 'fixed' ? 'd-none' : '' }}" id="valueSuffix">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Minimum Order Amount (Optional)</label>
                        <div class="input-group">
                            <span class="input-group-text">৳</span>
                            <input type="number" step="0.01" name="min_order_amount" class="form-control" value="{{ $promo->min_order_amount }}" placeholder="0.00">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Starts At (Required)</label>
                            <input type="datetime-local" name="starts_at" class="form-control" value="{{ $promo->starts_at?->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Expires At (Required)</label>
                            <input type="datetime-local" name="expires_at" class="form-control" value="{{ $promo->expires_at?->format('Y-m-d\TH:i') }}" required>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h5 class="fw-bold mb-3">Targeting</h5>

                    <div class="mb-3">
                        <label class="form-label">Applies To</label>
                        <select name="target_type" id="targetType" class="form-select">
                            <option value="all" {{ $promo->target_type == 'all' ? 'selected' : '' }}>All Orders</option>
                            <option value="category" {{ $promo->target_type == 'category' ? 'selected' : '' }}>Specific Categories</option>
                            <option value="product" {{ $promo->target_type == 'product' ? 'selected' : '' }}>Specific Products</option>
                        </select>
                    </div>

                    <div id="categorySelect" class="mb-3 {{ $promo->target_type == 'category' ? '' : 'd-none' }}">
                        <label class="form-label">Select Categories</label>
                        <select name="target_ids[]" class="form-select" multiple style="height: 150px;" {{ $promo->target_type == 'category' ? '' : 'disabled' }}>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ in_array($category->id, $promo->target_ids ?? []) ? 'selected' : '' }}>{{ $category->name }}</option>
                                @foreach($category->children as $child)
                                    <option value="{{ $child->id }}" {{ in_array($child->id, $promo->target_ids ?? []) ? 'selected' : '' }}>&nbsp;&nbsp;↳ {{ $child->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                        <div class="form-text">Hold Ctrl/Cmd to select multiple.</div>
                    </div>

                    <div id="productSelect" class="mb-3 {{ $promo->target_type == 'product' ? '' : 'd-none' }}">
                        <label class="form-label">Select Products</label>
                        <select name="target_ids[]" class="form-select" multiple style="height: 150px;" {{ $promo->target_type == 'product' ? '' : 'disabled' }}>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ in_array($product->id, $promo->target_ids ?? []) ? 'selected' : '' }}>{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Hold Ctrl/Cmd to select multiple.</div>
                    </div>


                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.promos.index') }}" class="btn btn-light">Back</a>
                        <button type="submit" class="btn btn-primary px-4">Update Promo Code</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const typeSelect = document.getElementById('typeSelect');
    const valuePrefix = document.getElementById('valuePrefix');
    const valueSuffix = document.getElementById('valueSuffix');

    typeSelect.addEventListener('change', function() {
        if (this.value === 'percent') {
            valuePrefix.classList.add('d-none');
            valueSuffix.classList.remove('d-none');
        } else {
            valuePrefix.classList.remove('d-none');
            valueSuffix.classList.add('d-none');
        }
    });

    const targetType = document.getElementById('targetType');
    const categorySelect = document.getElementById('categorySelect');
    const productSelect = document.getElementById('productSelect');

    targetType.addEventListener('change', function() {
        categorySelect.classList.add('d-none');
        productSelect.classList.add('d-none');
        
        document.querySelector('#categorySelect select').disabled = true;
        document.querySelector('#productSelect select').disabled = true;

        if (this.value === 'category') {
            categorySelect.classList.remove('d-none');
             document.querySelector('#categorySelect select').disabled = false;
        } else if (this.value === 'product') {
            productSelect.classList.remove('d-none');
             document.querySelector('#productSelect select').disabled = false;
        }
    });
</script>
@endsection
