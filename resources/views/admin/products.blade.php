@extends('layouts.admin')

@section('title', 'Produce Inventory Governance | MarketLink Admin')
@section('page-heading', 'Produce catalogue')

@section('content')
<div class="container mx-auto px-4 py-6">

    <div class="admin-action-row mb-4">
        <p>Manage harvest listings, pricing, and stock across all producer stalls.</p>
        <button class="btn btn-sm btn-forest px-3.5 py-2 font-bold" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="fa-solid fa-plus me-1"></i> Add Harvest Produce
        </button>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs mb-6">
        <form action="{{ route('admin.products') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-lg-4 col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-slate-50 border-end-0 text-slate-400">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control border-start-0 text-sm" placeholder="Search produce name or description...">
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <select name="category_id" class="form-select text-sm" onchange="this.form.submit()">
                    <option value="">All Crop Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ ($categoryId ?? '') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-3 col-md-6">
                <select name="farmer_id" class="form-select text-sm" onchange="this.form.submit()">
                    <option value="">All Farmer Stalls</option>
                    @foreach($farmers as $f)
                        <option value="{{ $f->id }}" {{ ($farmerId ?? '') == $f->id ? 'selected' : '' }}>
                            {{ $f->stall_name }} ({{ $f->stall_number }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-2 col-md-6 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-forest flex-grow-1">Filter</button>
                <a href="{{ route('admin.products') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden mb-6">
        <div class="p-4 border-b border-slate-100 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="font-bold text-slate-900 mb-0 font-heading">Regional Produce Catalogue</h5>
                <span class="text-xs text-slate-500">Live inventory across all certified smallholder stalls</span>
            </div>
            <span class="badge bg-slate-100 text-slate-700 border text-xs px-3 py-1.5 rounded-full font-bold">
                {{ $products->total() }} Total Harvest Items
            </span>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0 text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="ps-4">Produce</th>
                        <th>Producer Stall</th>
                        <th>Category</th>
                        <th>Standard Price</th>
                        <th>Zero-Waste Cut</th>
                        <th>Stock Units</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $prod)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $prod->image_url ?? 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=150&q=80' }}" alt="{{ $prod->name }}" class="rounded-xl object-cover" style="width: 46px; height: 46px;">
                                    <div>
                                        <a href="{{ route('product.show', $prod->slug) }}" target="_blank" class="font-bold text-slate-900 text-decoration-none hover:text-emerald-700">
                                            {{ $prod->name }}
                                        </a>
                                        <div class="text-xs text-slate-400">
                                            {{ $prod->harvest_label }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <strong class="text-slate-800 d-block">{{ $prod->farmerProfile->stall_name }}</strong>
                                <span class="badge bg-slate-100 text-slate-600 border text-[10px]">{{ $prod->farmerProfile->stall_number }}</span>
                            </td>
                            <td>
                                <span class="badge-organic text-xs">{{ $prod->category->name }}</span>
                            </td>
                            <td>
                                <span class="font-bold text-slate-900">${{ number_format($prod->price, 2) }}</span>
                                <span class="text-xs text-slate-400">/ {{ $prod->unit }}</span>
                            </td>
                            <td>
                                @if($prod->is_zero_waste_active)
                                    <span class="badge-flash-cut">
                                        <i class="fa-solid fa-bolt"></i> ${{ number_format($prod->zero_waste_price, 2) }} (-{{ $prod->discount_percent }}%)
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">Standard</span>
                                @endif
                                <form action="{{ route('admin.product.toggle_zero_waste', $prod->id) }}" method="POST" class="d-inline ms-1">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-link p-0 text-xs text-amber-600 text-decoration-none" title="Toggle Zero-Waste discount">
                                        {{ $prod->is_zero_waste_active ? '[Turn Off]' : '[Enable Cut]' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <strong class="{{ $prod->stock_quantity <= 5 ? 'text-danger' : 'text-slate-800' }}">
                                    {{ $prod->stock_quantity }}
                                </strong>
                                <span class="text-xs text-slate-400">{{ $prod->unit }}s</span>
                            </td>
                            <td>
                                <form action="{{ route('admin.product.toggle_status', $prod->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @if($prod->status === 'available')
                                        <button type="submit" class="badge bg-emerald-100 text-emerald-800 border border-emerald-200 border-0 cursor-pointer" title="Click to mark Sold Out">
                                            <i class="fa-solid fa-circle-check me-1 text-emerald-600"></i> Available
                                        </button>
                                    @else
                                        <button type="submit" class="badge bg-red-100 text-red-800 border border-red-200 border-0 cursor-pointer" title="Click to mark Available">
                                            <i class="fa-solid fa-circle-xmark me-1 text-red-600"></i> Sold Out
                                        </button>
                                    @endif
                                </form>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-1.5">
                                    <button class="btn btn-sm btn-outline-secondary rounded-lg px-2.5 py-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $prod->id }}" title="Edit Produce">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                    <form action="{{ route('admin.product.delete', $prod->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this produce listing from catalogue?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-lg px-2.5 py-1" title="Delete listing">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Edit Modal -->
                                <div class="modal fade text-start" id="editModal{{ $prod->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content rounded-3xl border-0 shadow-2xl">
                                            <div class="modal-header bg-slate-900 text-white p-4">
                                                <h5 class="modal-title font-bold text-base">Edit Produce: {{ $prod->name }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.product.update', $prod->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body p-4 space-y-3">
                                                    <div class="row g-3">
                                                        <div class="col-md-8">
                                                            <label class="form-label text-xs font-bold text-slate-700">Produce Name</label>
                                                            <input type="text" name="name" value="{{ $prod->name }}" class="form-control text-sm" required>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label text-xs font-bold text-slate-700">Category</label>
                                                            <select name="category_id" class="form-select text-sm" required>
                                                                @foreach($categories as $cat)
                                                                    <option value="{{ $cat->id }}" {{ $prod->category_id == $cat->id ? 'selected' : '' }}>
                                                                        {{ $cat->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label text-xs font-bold text-slate-700">Unit (e.g. bunch, lb, basket)</label>
                                                            <input type="text" name="unit" value="{{ $prod->unit }}" class="form-control text-sm" required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label text-xs font-bold text-slate-700">Price ($)</label>
                                                            <input type="number" step="0.01" name="price" value="{{ $prod->price }}" class="form-control text-sm" required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label text-xs font-bold text-slate-700">Zero-Waste Price ($)</label>
                                                            <input type="number" step="0.01" name="zero_waste_price" value="{{ $prod->zero_waste_price }}" class="form-control text-sm" placeholder="Optional">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label text-xs font-bold text-slate-700">Stock Units</label>
                                                            <input type="number" name="stock_quantity" value="{{ $prod->stock_quantity }}" class="form-control text-sm" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label text-xs font-bold text-slate-700">Status</label>
                                                            <select name="status" class="form-select text-sm" required>
                                                                <option value="available" {{ $prod->status == 'available' ? 'selected' : '' }}>Available</option>
                                                                <option value="sold_out" {{ $prod->status == 'sold_out' ? 'selected' : '' }}>Sold Out</option>
                                                                <option value="seasonal" {{ $prod->status == 'seasonal' ? 'selected' : '' }}>Seasonal Inactive</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label text-xs font-bold text-slate-700">Image URL</label>
                                                            <input type="url" name="image_url" value="{{ $prod->image_url }}" class="form-control text-sm">
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label text-xs font-bold text-slate-700">Harvest Notes</label>
                                                            <input type="text" name="harvest_notes" value="{{ $prod->harvest_notes }}" class="form-control text-sm">
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label text-xs font-bold text-slate-700">Description</label>
                                                            <textarea name="description" rows="3" class="form-control text-sm" required>{{ $prod->description }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-slate-50 p-3">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-sm btn-forest font-bold px-4">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-500 text-sm">
                                No produce found matching query criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $products->links() }}
        </div>
    </div>

</div>

<!-- Add Produce Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-3xl border-0 shadow-2xl">
            <div class="modal-header bg-emerald-700 text-white p-4">
                <h5 class="modal-title font-bold text-base">
                    <i class="fa-solid fa-basket-shopping me-1"></i> Add Harvest Produce to Catalogue
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.product.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-xs font-bold text-slate-700">Assign to Farmer Stall</label>
                            <select name="farmer_profile_id" class="form-select text-sm" required>
                                <option value="">Select Producer Stall</option>
                                @foreach($farmers as $f)
                                    <option value="{{ $f->id }}">{{ $f->stall_name }} ({{ $f->stall_number }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-xs font-bold text-slate-700">Category</label>
                            <select name="category_id" class="form-select text-sm" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label text-xs font-bold text-slate-700">Produce Name</label>
                            <input type="text" name="name" class="form-control text-sm" placeholder="e.g. Organic Honeycrisp Apples" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-xs font-bold text-slate-700">Unit Type</label>
                            <input type="text" name="unit" class="form-control text-sm" placeholder="e.g. bunch, lb, basket" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-xs font-bold text-slate-700">Standard Price ($)</label>
                            <input type="number" step="0.01" name="price" class="form-control text-sm" placeholder="e.g. 4.50" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-xs font-bold text-slate-700">Zero-Waste Discount Price ($)</label>
                            <input type="number" step="0.01" name="zero_waste_price" class="form-control text-sm" placeholder="Optional">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-xs font-bold text-slate-700">Stock Quantity</label>
                            <input type="number" name="stock_quantity" value="20" class="form-control text-sm" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-xs font-bold text-slate-700">Image URL</label>
                            <input type="url" name="image_url" class="form-control text-sm" placeholder="https://images.unsplash.com/..." value="https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=600&q=80">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-xs font-bold text-slate-700">Harvest Notes</label>
                            <input type="text" name="harvest_notes" class="form-control text-sm" placeholder="e.g. Cut by hand at 05:30 AM with roots intact">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-xs font-bold text-slate-700">Description</label>
                            <textarea name="description" rows="3" class="form-control text-sm" placeholder="Provide full details on freshness, soil conditions, and organic growing practices..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-slate-50 p-3">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-forest font-bold px-4">Publish Produce</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
