@extends('layouts.app')

@section('title', 'Inventory & Stock Matrix | ' . $farmer->stall_name)

@section('content')
<div class="container py-4">

    <!-- Header & Action Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-1">Inventory & Stock Matrix</h2>
            <p class="text-muted small mb-0">Control real-time harvest stock, trigger zero-waste batch discounts, and replicate menus</p>
        </div>

        <div class="d-flex gap-2">
            <!-- Feature 2: Weekly Stock Template Button -->
            <form action="{{ route('vendor.inventory.duplicate_weekly') }}" method="POST" onsubmit="return confirm('Apply Weekly Harvest Template? This will refresh harvest morning timestamps and replenish stock quantities across all current produce.')">
                @csrf
                <button type="submit" class="btn btn-sm btn-ochre">
                    <i class="fa-solid fa-copy me-1"></i> Apply Weekly Harvest Template
                </button>
            </form>

            <button type="button" class="btn btn-sm btn-forest" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="fa-solid fa-plus me-1"></i> Catalog New Produce
            </button>
        </div>
    </div>

    <!-- Inventory Matrix Table -->
    <div class="ml-card p-4">
        <div class="table-responsive">
            <table class="table align-middle small mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 28%;">Produce & Classification</th>
                        <th>Standard Price</th>
                        <th>Zero-Waste Auto-Discount</th>
                        <th>Available Stock</th>
                        <th>Harvest Timeline</th>
                        <th>In Stock Status</th>
                        <th style="width: 12%;" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($p->image_url)
                                        <img src="{{ $p->image_url }}" alt="" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                    @endif
                                    <div>
                                        <strong class="text-dark d-block">{{ $p->name }}</strong>
                                        <span class="text-muted" style="font-size: 0.75rem;">{{ $p->category ? $p->category->name : 'Uncategorized' }} &bull; {{ $p->unit }}</span>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="fw-bold">${{ number_format($p->price, 2) }}</span>
                                <span class="text-muted">/ {{ $p->unit }}</span>
                            </td>

                            <!-- Zero Waste Toggle & Price -->
                            <td>
                                <form action="{{ route('vendor.product.toggle_zero_waste', $p->id) }}" method="POST" class="d-flex align-items-center gap-2">
                                    @csrf
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="is_zero_waste_active" value="1" {{ $p->is_zero_waste_active ? 'checked' : '' }} onchange="this.form.submit()">
                                    </div>
                                    @if($p->is_zero_waste_active)
                                        <span class="badge-zero-waste">${{ number_format($p->zero_waste_price, 2) }}</span>
                                    @else
                                        <span class="text-muted small">Inactive</span>
                                    @endif
                                </form>
                            </td>

                            <td>
                                <span class="fw-semibold {{ $p->stock_quantity <= 5 ? 'text-danger' : 'text-dark' }}">
                                    {{ $p->stock_quantity }} {{ $p->unit }}s
                                </span>
                            </td>

                            <td>
                                <span class="badge-harvest-time">
                                    <i class="fa-solid fa-clock me-1"></i> {{ $p->harvest_status_string }}
                                </span>
                            </td>

                            <!-- Quick Stock Status Toggle -->
                            <td>
                                <form action="{{ route('vendor.product.toggle_status', $p->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm py-0 px-2 {{ $p->status === 'available' ? 'btn-sage' : 'btn-outline-danger' }}" style="font-size: 0.75rem;">
                                        {{ $p->status === 'available' ? 'In Stock' : 'Sold Out' }}
                                    </button>
                                </form>
                            </td>

                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <button class="btn btn-sm btn-outline-secondary py-0 px-2" data-bs-toggle="modal" data-bs-target="#editModal-{{ $p->id }}" title="Edit Produce">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <form action="{{ route('vendor.product.delete', $p->id) }}" method="POST" onsubmit="return confirm('Remove {{ addslashes($p->name) }} from harvest catalog?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Edit Product Modal -->
                        <div class="modal fade" id="editModal-{{ $p->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form action="{{ route('vendor.product.update', $p->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Produce: {{ $p->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-semibold">Produce Name</label>
                                                    <input type="text" name="name" value="{{ $p->name }}" class="form-control form-control-sm" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-semibold">Category</label>
                                                    <select name="category_id" class="form-select form-select-sm" required>
                                                        @foreach($categories as $cat)
                                                            <option value="{{ $cat->id }}" {{ $p->category_id == $cat->id ? 'selected' : '' }}>
                                                                {{ $cat->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row g-3 mb-3">
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-semibold">Unit (kg, lb, bunch, box)</label>
                                                    <input type="text" name="unit" value="{{ $p->unit }}" class="form-control form-control-sm" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-semibold">Standard Price ($)</label>
                                                    <input type="number" step="0.01" name="price" value="{{ $p->price }}" class="form-control form-control-sm" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-semibold">Zero-Waste Price ($)</label>
                                                    <input type="number" step="0.01" name="zero_waste_price" value="{{ $p->zero_waste_price }}" class="form-control form-control-sm" placeholder="Auto-discount price">
                                                </div>
                                            </div>

                                            <div class="row g-3 mb-3">
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-semibold">Stock Quantity</label>
                                                    <input type="number" name="stock_quantity" value="{{ $p->stock_quantity }}" class="form-control form-control-sm" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-semibold">CO2 Savings Offset (kg)</label>
                                                    <input type="number" step="0.01" name="co2_savings_kg" value="{{ $p->co2_savings_kg }}" class="form-control form-control-sm" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-semibold">Special Badge Label</label>
                                                    <input type="text" name="badge_label" value="{{ $p->badge_label }}" class="form-control form-control-sm" placeholder="e.g. Dawn Picked">
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Harvest Notes</label>
                                                <input type="text" name="harvest_notes" value="{{ $p->harvest_notes }}" class="form-control form-control-sm">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Image URL</label>
                                                <input type="url" name="image_url" value="{{ $p->image_url }}" class="form-control form-control-sm">
                                            </div>

                                            <div class="mb-0">
                                                <label class="form-label small fw-semibold">Description</label>
                                                <textarea name="description" rows="2" class="form-control form-control-sm">{{ $p->description }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-sm btn-forest">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No produce listed in harvest catalog yet. Click "Catalog New Produce" above.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Add Produce Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('vendor.product.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Catalog New Harvest Produce</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Produce Name</label>
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Hydroponic Butterhead Lettuce" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Category</label>
                            <select name="category_id" class="form-select form-select-sm" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Unit (kg, lb, head, dozen, box)</label>
                            <input type="text" name="unit" value="kg" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Standard Price ($)</label>
                            <input type="number" step="0.01" name="price" value="5.00" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Zero-Waste Discount Price ($)</label>
                            <input type="number" step="0.01" name="zero_waste_price" value="3.25" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Initial Stock Quantity</label>
                            <input type="number" name="stock_quantity" value="20" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">CO2 Offset (kg per unit)</label>
                            <input type="number" step="0.01" name="co2_savings_kg" value="1.80" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Badge Label (Optional)</label>
                            <input type="text" name="badge_label" class="form-control form-control-sm" placeholder="e.g. Dawn Picked">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Harvest Notes</label>
                        <input type="text" name="harvest_notes" value="Hand-picked this morning with soil intact." class="form-control form-control-sm">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Image Preview URL</label>
                        <input type="url" name="image_url" value="https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=600&q=80" class="form-control form-control-sm">
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold">Description</label>
                        <textarea name="description" rows="2" class="form-control form-control-sm">Crisp, certified pesticide-free crop grown in mineral-rich regional soil.</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-forest">Catalog Produce</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
