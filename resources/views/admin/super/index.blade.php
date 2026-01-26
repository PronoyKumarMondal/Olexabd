@extends('layouts.admin')

@section('header', 'User Management (Super Admin)')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Admin Management</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAdminModal">
        <i class="bi bi-person-plus-fill me-2"></i>Create New Admin
    </button>
</div>

<!-- Create Admin Modal -->
<div class="modal fade" id="createAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.super.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create New Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" onchange="togglePermissionsCreate(this)">
                            <option value="admin">Admin (Staff)</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>

                    <div id="create-permissions">
                        <label class="form-label fw-bold">Permissions</label>
                        <div class="card p-3 bg-light border-0" style="max-height: 200px; overflow-y: auto;">
                            <!-- Product Permissions -->
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="product_create" id="new_perm_product_create">
                                <label class="form-check-label" for="new_perm_product_create">Create Products</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="product_edit" id="new_perm_product_edit">
                                <label class="form-check-label" for="new_perm_product_edit">Edit Products</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="product_delete" id="new_perm_product_delete">
                                <label class="form-check-label" for="new_perm_product_delete">Delete Products</label>
                            </div>

                            <!-- Category Permissions -->
                            <hr class="my-2">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="category_create" id="new_perm_category_create">
                                <label class="form-check-label" for="new_perm_category_create">Create Categories</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="category_edit" id="new_perm_category_edit">
                                <label class="form-check-label" for="new_perm_category_edit">Edit Categories</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="category_delete" id="new_perm_category_delete">
                                <label class="form-check-label" for="new_perm_category_delete">Delete Categories</label>
                            </div>

                            <!-- Order Permissions -->
                            <hr class="my-2">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="order_create" id="new_perm_order_create">
                                <label class="form-check-label" for="new_perm_order_create">Create Orders</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="order_edit" id="new_perm_order_edit">
                                <label class="form-check-label" for="new_perm_order_edit">Manage Order Status</label>
                            </div>

                            <!-- Customer Permissions -->
                            <hr class="my-2">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="view_customers" id="new_perm_view_customers">
                                <label class="form-check-label" for="new_perm_view_customers">View Customers</label>
                            </div>

                            <!-- Content Permissions -->
                            <hr class="my-2">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="manage_banners" id="new_perm_manage_banners">
                                <label class="form-check-label" for="new_perm_manage_banners">Manage Banners</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="manage_promos" id="new_perm_manage_promos">
                                <label class="form-check-label" for="new_perm_manage_promos">Manage Promo Codes</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="manage_discounts" id="new_perm_manage_discounts">
                                <label class="form-check-label" for="new_perm_manage_discounts">Manage Product Discounts</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="view_cart_history" id="new_perm_view_cart_history">
                                <label class="form-check-label" for="new_perm_view_cart_history">View Cart History</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="view_search_history" id="new_perm_view_search_history">
                                <label class="form-check-label" for="new_perm_view_search_history">View Search History</label>
                            </div>
                            
                            <hr class="my-2">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="manage_settings" id="new_perm_manage_settings">
                                <label class="form-check-label" for="new_perm_manage_settings">Manage System Settings (Delivery Charge)</label>
                            </div>

                            <label class="form-label text-muted d-block mt-2 small">Channels</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="manage_channels" id="new_perm_manage_channels">
                                <label class="form-check-label" for="new_perm_manage_channels">Manage Channels</label>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-1">Super Admins have all permissions automatically.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Email</th>
                        <th>Current Role</th>
                        <th>Permissions</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->role === 'super_admin')
                                <span class="badge bg-danger">Super Admin</span>
                            @else
                                <span class="badge bg-primary">Admin</span>
                            @endif
                        </td>
                        <td>
                            @if($user->role === 'admin' && $user->permissions)
                                @foreach($user->permissions as $perm)
                                    <span class="badge bg-light text-dark border">{{ str_replace(['product_', 'category_'], '', $perm) }}</span>
                                @endforeach
                            @elseif($user->role === 'super_admin')
                                <span class="badge bg-success">All Access</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#editUser{{ $user->id }}">
                                Manage
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editUser{{ $user->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.super.update_role', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Manage {{ $user->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Role</label>
                                            <select name="role" class="form-select" onchange="togglePermissions(this, {{ $user->id }})">
                                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin (Staff)</option>
                                                <option value="super_admin" {{ $user->role === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                            </select>
                                        </div>

                                        <div id="permissions-{{ $user->id }}" class="{{ $user->role !== 'admin' ? 'd-none' : '' }}">
                                            <label class="form-label fw-bold">Permissions</label>
                                            <div class="card p-3 bg-light border-0">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="product_create" id="perm_create_{{ $user->id }}"
                                                        {{ in_array('product_create', $user->permissions ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_create_{{ $user->id }}">Create Products</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="product_edit" id="perm_edit_{{ $user->id }}"
                                                        {{ in_array('product_edit', $user->permissions ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_edit_{{ $user->id }}">Edit Products</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="product_delete" id="perm_delete_{{ $user->id }}"
                                                        {{ in_array('product_delete', $user->permissions ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_delete_{{ $user->id }}">Delete Products</label>
                                                </div>
                                            </div>
                                            <label class="form-label fw-bold mt-2">Category Permissions</label>
                                            <div class="card p-3 bg-light border-0">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="category_create" id="perm_cat_create_{{ $user->id }}"
                                                        {{ in_array('category_create', $user->permissions ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_cat_create_{{ $user->id }}">Create Categories</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="category_edit" id="perm_cat_edit_{{ $user->id }}"
                                                        {{ in_array('category_edit', $user->permissions ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_cat_edit_{{ $user->id }}">Edit Categories</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="category_delete" id="perm_cat_delete_{{ $user->id }}"
                                                        {{ in_array('category_delete', $user->permissions ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_cat_delete_{{ $user->id }}">Delete Categories</label>
                                                </div>
                                            </div>

                                            <label class="form-label fw-bold mt-2">Order Permissions</label>
                                            <div class="card p-3 bg-light border-0">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="order_create" id="perm_order_create_{{ $user->id }}"
                                                        {{ in_array('order_create', $user->permissions ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_order_create_{{ $user->id }}">Create Orders</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="order_edit" id="perm_order_edit_{{ $user->id }}"
                                                        {{ in_array('order_edit', $user->permissions ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_order_edit_{{ $user->id }}">Manage Order Status (Process/Ship)</label>
                                                </div>
                                            </div>

                                            <label class="form-label fw-bold mt-2">Customer Permissions</label>
                                            <div class="card p-3 bg-light border-0">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="view_customers" id="perm_view_customers_{{ $user->id }}"
                                                        {{ in_array('view_customers', $user->permissions ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_view_customers_{{ $user->id }}">View Customers</label>
                                                </div>
                                            </div>

                                            <label class="form-label fw-bold mt-2">Content Permissions</label>
                                            <div class="card p-3 bg-light border-0">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="manage_banners" id="perm_manage_banners_{{ $user->id }}"
                                                        {{ in_array('manage_banners', $user->permissions ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_manage_banners_{{ $user->id }}">Manage Banners (Homepage)</label>
                                                </div>
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="manage_promos" id="perm_manage_promos_{{ $user->id }}"
                                                        {{ in_array('manage_promos', $user->permissions ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_manage_promos_{{ $user->id }}">Manage Promo Codes</label>
                                                </div>
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="manage_discounts" id="perm_manage_discounts_{{ $user->id }}"
                                                        {{ in_array('manage_discounts', $user->permissions ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_manage_discounts_{{ $user->id }}">Manage Product Discounts</label>
                                                </div>
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="view_cart_history" id="perm_view_cart_history_{{ $user->id }}"
                                                        {{ in_array('view_cart_history', $user->permissions ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_view_cart_history_{{ $user->id }}">View Cart History</label>
                                                </div>
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="view_search_history" id="perm_view_search_history_{{ $user->id }}"
                                                        {{ in_array('view_search_history', $user->permissions ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_view_search_history_{{ $user->id }}">View Search History</label>
                                                </div>

                                                <div class="form-check mt-2 border-top pt-2">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="manage_settings" id="perm_manage_settings_{{ $user->id }}"
                                                        {{ in_array('manage_settings', $user->permissions ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_manage_settings_{{ $user->id }}">Manage System Settings (Delivery Charge)</label>
                                                </div>

                                                <div class="form-check mt-2 border-top pt-2">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="manage_channels" id="perm_manage_channels_{{ $user->id }}"
                                                        {{ in_array('manage_channels', $user->permissions ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_manage_channels_{{ $user->id }}">Manage Channels</label>
                                                </div>
                                                </div>
                                            </div>
                                            <small class="text-muted">Super Admins have all permissions by default.</small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function togglePermissions(select, userId) {
        const permDiv = document.getElementById('permissions-' + userId);
        if (select.value === 'admin') {
            permDiv.classList.remove('d-none');
        } else {
            permDiv.classList.add('d-none');
        }
    }

    function togglePermissionsCreate(select) {
        const permDiv = document.getElementById('create-permissions');
        if (select.value === 'admin') {
            permDiv.classList.remove('d-none');
        } else {
            permDiv.classList.add('d-none');
        }
    }
</script>
@endsection
