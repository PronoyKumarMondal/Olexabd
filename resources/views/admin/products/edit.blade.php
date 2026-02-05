@extends('layouts.admin')

@section('header', 'Edit Product')

@section('content')
<div class="card border-0 shadow-sm mw-100" style="max-width: 800px;">
    <div class="card-body">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Category <span class="text-danger">*</span></label>
                    <select name="category_id" id="main_category" class="form-select" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sub Category (Optional)</label>
                    <select name="sub_category_id" id="sub_category" class="form-select" {{ $product->category_id ? '' : 'disabled' }}>
                        <option value="">Select Sub Category</option>
                        {{-- Pre-populate if parent is selected --}}
                        @if($product->category_id)
                            @php
                                $parent = $categories->find($product->category_id);
                            @endphp
                            @if($parent && $parent->children->count() > 0)
                                @foreach($parent->children as $child)
                                    <option value="{{ $child->id }}" {{ $product->sub_category_id == $child->id ? 'selected' : '' }}>{{ $child->name }}</option>
                                @endforeach
                            @endif
                        @endif
                    </select>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const categories = @json($categories);
                    const mainCat = document.getElementById('main_category');
                    const subCat = document.getElementById('sub_category');

                    mainCat.addEventListener('change', function() {
                        const selectedId = this.value;
                        
                        subCat.innerHTML = '<option value="">Select Sub Category</option>';
                        subCat.disabled = true;

                        if (selectedId) {
                            const category = categories.find(c => c.id == selectedId);
                            if (category && category.children && category.children.length > 0) {
                                subCat.disabled = false;
                                category.children.forEach(child => {
                                    const option = document.createElement('option');
                                    option.value = child.id;
                                    option.textContent = child.name;
                                    subCat.appendChild(option);
                                });
                            }
                        }
                    });
                });
            </script>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Price <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ $product->price }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Stock <span class="text-danger">*</span></label>
                    <input type="number" name="stock" class="form-control" value="{{ $product->stock }}" required>
                </div>
            </div>

            <div class="card bg-light border-0 mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-percent me-2"></i>Revenue Margin</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Commission Percentage (%)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="commission_percentage" id="commission_percent" class="form-control" placeholder="e.g. 10" value="{{ $product->commission_percentage }}">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Commission Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">৳</span>
                                <input type="number" step="0.01" name="commission_amount" id="commission_amount" class="form-control" placeholder="e.g. 100" value="{{ $product->commission_amount }}">
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
                                <input type="number" step="0.01" name="discount_price" id="discountPrice" class="form-control" value="{{ $product->discount_price }}">
                            </div>
                            <div class="form-text text-success fw-bold" id="discountPercent"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="datetime-local" name="discount_start" class="form-control" value="{{ $product->discount_start?->format('Y-m-d\TH:i') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="datetime-local" name="discount_end" class="form-control" value="{{ $product->discount_end?->format('Y-m-d\TH:i') }}">
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" rows="4">{{ $product->description }}</textarea>
            </div>

            <!-- Drag & Drop Image Upload -->
            <div class="mb-3">
                <label class="form-label">Product Image</label>
                @if($product->image)
                    <div class="mb-2">
                        <img src="{{ $product->image }}" alt="Current Image" style="height: 100px; border-radius: 5px;">
                        <span class="text-muted small ms-2">Current Image</span>
                    </div>
                @endif

                <div class="image-upload-zone p-4 text-center border rounded bg-light" style="border: 2px dashed #ccc !important; cursor: pointer;" onclick="document.getElementById('imageInput').click()">
                    <i class="bi bi-cloud-arrow-up fs-2 text-primary"></i>
                    <p class="mb-0 text-muted mt-2"><strong>Click to upload</strong> or drag and drop</p>
                    <p class="small text-muted mb-0">SVG, PNG, JPG or GIF (MAX. 2MB)</p>
                    <input type="file" name="image_file" id="imageInput" class="d-none" accept="image/*" onchange="previewImage(this)">
                </div>
                <div id="preview-area" class="mt-2 d-none">
                    <p class="small text-success mb-1"><i class="bi bi-check-circle"></i> File selected:</p>
                    <span id="file-name" class="fw-bold"></span>
                </div>
            </div>

            <!-- Featured Images -->
            <div class="mb-3">
                <label class="form-label">Featured Images (Gallery)</label>
                
                @if($product->images->count() > 0)
                <div class="row g-2 mb-2">
                    @foreach($product->images as $img)
                    <div class="col-md-3 position-relative">
                        <img src="{{ $img->image_path }}" class="img-thumbnail" style="height: 100px; width: 100%; object-fit: cover;">
                        <div class="form-check mt-1">
                            <input class="form-check-input" type="checkbox" name="delete_images[]" value="{{ $img->id }}" id="del_{{ $img->id }}">
                            <label class="form-check-label text-danger small" for="del_{{ $img->id }}">Delete</label>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                <div class="mt-2">
                    <label class="form-label small text-muted">Add Additional Images (Variable)</label>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="small text-muted mb-1">New Image 1</label>
                            <input type="file" name="featured_images[]" class="form-control form-control-sm" accept="image/*" onchange="previewIndividual(this, 'previewNew1')">
                            <img id="previewNew1" class="img-thumbnail mt-2 d-none" style="height: 60px; width: 60px; object-fit: cover;">
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted mb-1">New Image 2</label>
                            <input type="file" name="featured_images[]" class="form-control form-control-sm" accept="image/*" onchange="previewIndividual(this, 'previewNew2')">
                            <img id="previewNew2" class="img-thumbnail mt-2 d-none" style="height: 60px; width: 60px; object-fit: cover;">
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted mb-1">New Image 3</label>
                            <input type="file" name="featured_images[]" class="form-control form-control-sm" accept="image/*" onchange="previewIndividual(this, 'previewNew3')">
                            <img id="previewNew3" class="img-thumbnail mt-2 d-none" style="height: 60px; width: 60px; object-fit: cover;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_featured">Featured Product</label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active (Visible)</label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_free_delivery" id="is_free_delivery" value="1" {{ old('is_free_delivery', $product->is_free_delivery) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_free_delivery">Free Delivery</label>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.products.index') }}" class="btn btn-light">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Product</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // CKEditor
        ClassicEditor.create(document.querySelector('#description'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo']
        }).catch(error => console.error(error));

        // Drag & Drop
        const dropZone = document.querySelector('.image-upload-zone');
        const input = document.getElementById('imageInput');
        const previewArea = document.getElementById('preview-area');
        const fileNameDisplay = document.getElementById('file-name');

        if(dropZone && input) {
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
        }

        window.previewImage = function(input) {
            if (input.files && input.files[0]) {
                fileNameDisplay.innerText = input.files[0].name;
                previewArea.classList.remove('d-none');
            }
        };

        window.previewIndividual = function(input, imgId) {
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
        };

        // Price Input Shared Reference
        const priceInput = document.querySelector('input[name="price"]');

        // Discount Calculation
        const discountInput = document.getElementById('discountPrice');
        const percentDisplay = document.getElementById('discountPercent');

        function calcDiscount() {
            if(!discountInput || !priceInput) return;
            
            const price = parseFloat(priceInput.value) || 0;
            const discount = parseFloat(discountInput.value) || 0;

            if (price > 0 && discount > 0 && discount < price) {
                const percent = Math.round(((price - discount) / price) * 100);
                percentDisplay.innerText = percent + "% OFF";
            } else {
                if(percentDisplay) percentDisplay.innerText = "";
            }
        }

        if(priceInput && discountInput) {
            priceInput.addEventListener('input', calcDiscount);
            discountInput.addEventListener('input', calcDiscount);
            calcDiscount(); // Initial Run
        }

        // Commission Calculation
        const commPercent = document.getElementById('commission_percent');
        const commAmount = document.getElementById('commission_amount');

        function calcCommission(source) {
            if(!priceInput) return;
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
                if(commPercent.value) calcCommission('percent');
            });
            // Initial Check (for edit mode generally, but good practice)
            if(commPercent.value) calcCommission('percent');
        }
    });
</script>
@endsection
