@extends('layouts.admin')

@section('header', 'Add Product')

@section('content')
<div class="card border-0 shadow-sm mw-100" style="max-width: 800px;">
    <div class="card-body">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Category <span class="text-danger">*</span></label>
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
                    <label class="form-label">Price <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Stock <span class="text-danger">*</span></label>
                    <input type="number" name="stock" class="form-control" required>
                </div>
            </div>

            <div class="card bg-light border-0 mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-percent me-2"></i>Commission Margin</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Commission Percentage (%)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="commission_percentage" id="commission_percent" class="form-control" placeholder="e.g. 10">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Commission Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">৳</span>
                                <input type="number" step="0.01" name="commission_amount" id="commission_amount" class="form-control" placeholder="e.g. 100">
                            </div>
                        </div>
                    </div>
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
                <textarea name="description" id="description" class="form-control" rows="4"></textarea>
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

            <!-- Featured Images (Max 3) -->
            <!-- Featured Images (Max 3) -->
            <div class="mb-3">
                <label class="form-label fw-bold">Featured Images (Optional)</label>
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="small text-muted mb-1">Image 1</label>
                        <input type="file" name="featured_images[]" class="form-control form-control-sm" accept="image/*" onchange="previewIndividual(this, 'preview1')">
                        <img id="preview1" class="img-thumbnail mt-2 d-none" style="height: 60px; width: 60px; object-fit: cover;">
                    </div>
                    <div class="col-md-4">
                        <label class="small text-muted mb-1">Image 2</label>
                        <input type="file" name="featured_images[]" class="form-control form-control-sm" accept="image/*" onchange="previewIndividual(this, 'preview2')">
                        <img id="preview2" class="img-thumbnail mt-2 d-none" style="height: 60px; width: 60px; object-fit: cover;">
                    </div>
                    <div class="col-md-4">
                        <label class="small text-muted mb-1">Image 3</label>
                        <input type="file" name="featured_images[]" class="form-control form-control-sm" accept="image/*" onchange="previewIndividual(this, 'preview3')">
                        <img id="preview3" class="img-thumbnail mt-2 d-none" style="height: 60px; width: 60px; object-fit: cover;">
                    </div>
                </div>
                <div class="form-text mt-1">Select up to 3 images for the gallery.</div>
            </div>

            <div class="mb-4 d-flex gap-4">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_featured">Featured Product</label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active (Visible)</label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_free_delivery" id="is_free_delivery" value="1" {{ old('is_free_delivery') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_free_delivery">Free Delivery</label>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.products.index') }}" class="btn btn-light">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Product</button>
            </div>
        </form>
    </div>
</div>


<!-- CKEditor 5 -->
<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        ClassicEditor
            .create(document.querySelector('#description'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo'],
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                    ]
                }
            })
            .catch(error => {
                console.error(error);
            });
    });

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

    function previewIndividual(input, imgId) {
        const img = document.getElementById(imgId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                img.classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            img.src = '';
            img.classList.add('d-none');
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

    // Commission Calculation
    const commPercent = document.getElementById('commission_percent');
    const commAmount = document.getElementById('commission_amount');

    function calcCommission(source) {
        const price = parseFloat(priceInput.value) || 0;
        
        if (price <= 0) return;

        if (source === 'percent') {
            const percent = parseFloat(commPercent.value) || 0;
            const amount = (price * percent) / 100;
            commAmount.value = amount.toFixed(2);
        } else if (source === 'amount') {
            const amount = parseFloat(commAmount.value) || 0;
            const percent = (amount / price) * 100;
            commPercent.value = percent.toFixed(2);
        }
    }

    if (commPercent && commAmount && priceInput) {
        commPercent.addEventListener('input', () => calcCommission('percent'));
        commAmount.addEventListener('input', () => calcCommission('amount'));
        priceInput.addEventListener('input', () => {
            // Update amounts if price changes but percent stays correct? 
            // Usually keep % constant
            if(commPercent.value) calcCommission('percent');
        });
    }
</script>
@endsection
