@extends('layouts.admin')

@section('header', 'Add Product')

@section('content')
<div class="card border-0 shadow-sm mw-100" style="max-width: 800px;">
    <div class="card-body">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Product Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $parent)
                        <option value="{{ $parent->id }}" class="fw-bold">{{ $parent->name }}</option>
                        @foreach($parent->children as $child)
                            <option value="{{ $child->id }}">{{ $parent->name }} / {{ $child->name }}</option>
                        @endforeach
                    @endforeach
                </select>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Stock</label>
                    <input type="number" name="stock" class="form-control" required>
                </div>
            </div>

            @if(auth('admin')->user()->hasPermission('manage_discounts'))
            <div class="card bg-light border-0 mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-tag-fill me-2"></i>Discount Configuration</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Discount Price</label>
                            <div class="input-group">
                                <span class="input-group-text">৳</span>
                                <input type="number" step="0.01" name="discount_price" id="discountPrice" class="form-control">
                            </div>
                            <div class="form-text text-success fw-bold" id="discountPercent"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="datetime-local" name="discount_start" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="datetime-local" name="discount_end" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4"></textarea>
            </div>

            <!-- Drag & Drop Image Upload -->
            <div class="mb-3">
                <label class="form-label">Product Image</label>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Product Image (Upload)</label>
                        <div class="input-group">
                            <input type="file" name="image_file" id="imageInput" class="form-control" accept="image/*" onchange="previewImage(this)">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                         <label class="form-label">OR Image Link (URL)</label>
                         <input type="url" name="image_url" class="form-control" placeholder="https://">
                    </div>
                </div>
                <div id="preview-area" class="mt-2 d-none">
                    <p class="small text-success mb-1"><i class="bi bi-check-circle"></i> File selected:</p>
                    <span id="file-name" class="fw-bold"></span>
                </div>
            </div>

            <div class="mb-4 d-flex gap-4">
                <div class="form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" id="activeCheck" checked value="1">
                    <label class="form-check-label" for="activeCheck">Active</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="is_featured" class="form-check-input" id="featCheck" value="1">
                    <label class="form-check-label" for="featCheck">Featured</label>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.products.index') }}" class="btn btn-light">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Product</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Simple Drag & Drop & Preview Script
    const dropZone = document.querySelector('.image-upload-zone');
    const input = document.getElementById('imageInput');

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.style.borderColor = '#4f46e5';
        dropZone.style.backgroundColor = '#eef2ff';
    });

    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropZone.style.borderColor = '#ccc';
        dropZone.style.backgroundColor = '#f8f9fa';
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.style.borderColor = '#ccc';
        dropZone.style.backgroundColor = '#f8f9fa';
        
        if (e.dataTransfer.files.length) {
            input.files = e.dataTransfer.files;
            previewImage(input);
        }
    });

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            document.getElementById('file-name').innerText = fileName;
            document.getElementById('preview-area').classList.remove('d-none');
        }
    }

    // Discount Calculation
    const priceInput = document.querySelector('input[name="price"]');
    const discountInput = document.getElementById('discountPrice');
    const percentDisplay = document.getElementById('discountPercent');

    function calcDiscount() {
        if(!discountInput) return; // If permission denied, element won't exist
        
        const price = parseFloat(priceInput.value) || 0;
        const discount = parseFloat(discountInput.value) || 0;

        if (price > 0 && discount > 0 && discount < price) {
            const percent = Math.round(((price - discount) / price) * 100);
            percentDisplay.innerText = percent + "% OFF";
        } else {
            percentDisplay.innerText = "";
        }
    }

    if(priceInput && discountInput) {
        priceInput.addEventListener('input', calcDiscount);
        discountInput.addEventListener('input', calcDiscount);
    }
</script>
@endsection
