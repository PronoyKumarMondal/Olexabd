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
                        <input type="text" class="form-control mb-2" id="categorySearch" placeholder="Search categories..." onkeyup="filterList('categorySearch', 'categoryList')">
                        <div class="border rounded p-3 overflow-auto bg-white" style="height: 200px;" id="categoryList">
                            @foreach($categories as $category)
                                <div class="form-check search-item">
                                    <input class="form-check-input" type="checkbox" name="target_ids[]" value="{{ $category->id }}" id="cat{{$category->id}}" {{ in_array($category->id, $promo->target_ids ?? []) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="cat{{$category->id}}">{{ $category->name }}</label>
                                </div>
                                @foreach($category->children as $child)
                                <div class="form-check search-item ms-4">
                                    <input class="form-check-input" type="checkbox" name="target_ids[]" value="{{ $child->id }}" id="cat{{$child->id}}" {{ in_array($child->id, $promo->target_ids ?? []) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="cat{{$child->id}}">↳ {{ $child->name }}</label>
                                </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>

                    <div id="productSelect" class="mb-3 {{ $promo->target_type == 'product' ? '' : 'd-none' }}">
                        <label class="form-label">Select Products</label>
                        <input type="text" class="form-control mb-2" id="productSearch" placeholder="Search products..." onkeyup="filterList('productSearch', 'productList')">
                         <div class="border rounded p-3 overflow-auto bg-white" style="height: 200px;" id="productList">
                            @foreach($products as $product)
                                <div class="form-check search-item">
                                    <input class="form-check-input" type="checkbox" name="target_ids[]" value="{{ $product->id }}" id="prod{{$product->id}}" {{ in_array($product->id, $promo->target_ids ?? []) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="prod{{$product->id}}">{{ $product->name }}</label>
                                </div>
                            @endforeach
                        </div>
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
        
        disableInputsIn(categorySelect, true);
        disableInputsIn(productSelect, true);

        if (this.value === 'category') {
            categorySelect.classList.remove('d-none');
             disableInputsIn(categorySelect, false);
        } else if (this.value === 'product') {
            productSelect.classList.remove('d-none');
             disableInputsIn(productSelect, false);
        }
    });

    function disableInputsIn(element, disabled) {
        const inputs = element.querySelectorAll('input[type="checkbox"]');
        inputs.forEach(input => input.disabled = disabled);
    }

    // Init Logic on Load
    // We need to enable the inputs of the currently visible section
     if (targetType.value === 'category') {
        disableInputsIn(categorySelect, false);
        disableInputsIn(productSelect, true); 
    } else if (targetType.value === 'product') {
        disableInputsIn(productSelect, false);
        disableInputsIn(categorySelect, true);
    } else {
        disableInputsIn(categorySelect, true);
        disableInputsIn(productSelect, true);
    }

     // Search Function
    function filterList(inputId, listId) {
        const input = document.getElementById(inputId);
        const filter = input.value.toLowerCase();
        const list = document.getElementById(listId);
        const items = list.getElementsByClassName('search-item');

        for (let i = 0; i < items.length; i++) {
            const label = items[i].getElementsByTagName("label")[0];
            const txtValue = label.textContent || label.innerText;
            if (txtValue.toLowerCase().indexOf(filter) > -1) {
                items[i].classList.remove('d-none');
            } else {
                items[i].classList.add('d-none');
            }
        }
    }
</script>
@endsection
