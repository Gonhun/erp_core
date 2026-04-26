<div>
    <style>
        .odoo-table {
            border-bottom: 1px solid #dee2e6;
        }
        .odoo-table th {
            color: #495057;
            font-weight: 600;
            font-size: 0.9rem;
            border-top: none;
            border-bottom: 2px solid #dee2e6;
            padding: 12px 16px;
        }
        .odoo-table td {
            vertical-align: middle;
            color: #212529;
            padding: 8px 16px;
            border-bottom: 1px solid #e9ecef;
        }
        .btn-new {
            background-color: #008784;
            color: white;
            border-radius: 2px;
            font-weight: bold;
            padding: 6px 16px;
        }
        .btn-new:hover {
            background-color: #006e6b;
            color: white;
        }
        .action-text {
            color: #008784;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }
        .action-text:hover {
            color: #006e6b;
        }
        .form-control:focus, .form-select:focus {
            border-color: #008784;
            box-shadow: 0 0 0 0.25rem rgba(0, 135, 132, 0.25);
        }
    </style>
    
    <div class="container-fluid py-3 bg-white">
        <div class="mb-4">
            <a href="{{ route('inventory.home') }}" class="text-muted text-decoration-none">
                <i class="mdi mdi-arrow-left"></i> Back to Inventory
            </a>
            <h2 class="mt-2">Products</h2>
        </div>

        @if($isCreating || $editingId)
            <!-- Form View -->
            <div class="card mb-4 border-0 shadow-sm" style="border: 1px solid #dee2e6 !important;" wire:key="form-card-{{ $iteration }}">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0">{{ $isCreating ? 'New Product' : 'Edit Product' }}</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold">Product Code</label>
                            <input type="text" class="form-control bg-light" wire:model="{{ $isCreating ? 'new_product_code' : 'edit_product_code' }}" {{ $isCreating ? 'readonly' : '' }}>
                            @error($isCreating ? 'new_product_code' : 'edit_product_code') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Product Name</label>
                            <input type="text" class="form-control" wire:model="{{ $isCreating ? 'new_product_name' : 'edit_product_name' }}">
                            @error($isCreating ? 'new_product_name' : 'edit_product_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold">Part Number</label>
                            <input type="text" class="form-control" wire:model="{{ $isCreating ? 'new_part_number' : 'edit_part_number' }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Product Type</label>
                            <select class="form-select" wire:model="{{ $isCreating ? 'new_product_type_id' : 'edit_product_type_id' }}">
                                <option value=""></option>
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->product_type_name }}</option>
                                @endforeach
                            </select>
                            @error($isCreating ? 'new_product_type_id' : 'edit_product_type_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Invoicing Policy</label>
                            <select class="form-select" wire:model="{{ $isCreating ? 'new_invoicing_policy' : 'edit_invoicing_policy' }}">
                                <option value="Ordered">Ordered quantities</option>
                                <option value="Delivered">Delivered quantities</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Brand</label>
                            <select class="form-select" wire:model="{{ $isCreating ? 'new_brand_id' : 'edit_brand_id' }}">
                                <option value=""></option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Unit of Measure</label>
                            <select class="form-select" wire:model="{{ $isCreating ? 'new_uom_id' : 'edit_uom_id' }}">
                                <option value=""></option>
                                @foreach($uoms as $uom)
                                    <option value="{{ $uom->id }}">{{ $uom->uom_name }}</option>
                                @endforeach
                            </select>
                            @error($isCreating ? 'new_uom_id' : 'edit_uom_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Purchase Unit of Measure</label>
                            <select class="form-select" wire:model="{{ $isCreating ? 'new_purchase_uom_id' : 'edit_purchase_uom_id' }}">
                                <option value=""></option>
                                @foreach($uoms as $uom)
                                    <option value="{{ $uom->id }}">{{ $uom->uom_name }}</option>
                                @endforeach
                            </select>
                            @error($isCreating ? 'new_purchase_uom_id' : 'edit_purchase_uom_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4"></div>

                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Item Category</label>
                            <select class="form-select" wire:model.live="{{ $isCreating ? 'new_item_category_id' : 'edit_item_category_id' }}">
                                <option value=""></option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                @endforeach
                            </select>
                            @error($isCreating ? 'new_item_category_id' : 'edit_item_category_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Sub Item Category</label>
                            <select class="form-select" wire:model="{{ $isCreating ? 'new_sub_item_id' : 'edit_sub_item_id' }}">
                                <option value=""></option>
                                @foreach(($isCreating ? $newSubCategories : $editSubCategories) as $sub)
                                    <option value="{{ $sub->id }}">{{ $sub->sub_category_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold">Internal Notes</label>
                            <textarea class="form-control" rows="3" wire:model="{{ $isCreating ? 'new_product_notes' : 'edit_product_notes' }}"></textarea>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex gap-2">
                        @if($isCreating)
                            <button wire:click="saveNew" wire:confirm="Are you sure you want to create this product?" class="btn btn-new">SAVE</button>
                            <button wire:click="cancelNew" class="btn btn-light border">DISCARD</button>
                        @else
                            <button wire:click="saveEdit" wire:confirm="Are you sure you want to save these changes?" class="btn btn-new">SAVE CHANGES</button>
                            <button wire:click="cancelEdit" class="btn btn-light border">DISCARD</button>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <!-- List View -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <button wire:click="createNew" class="btn btn-new border-0 shadow-none me-2">NEW PRODUCT</button>
                </div>
                <div class="text-muted small d-flex align-items-center">
                    <span class="me-3"><i class="mdi mdi-filter-variant"></i> Filters</span>
                    <span class="me-3"><i class="mdi mdi-format-list-bulleted-type"></i> Group By</span>
                    <span><i class="mdi mdi-star"></i> Favorites</span>
                </div>
                <div class="text-muted small">
                    1-{{ count($products) }} / {{ count($products) }}
                    <i class="mdi mdi-chevron-left ms-2 fs-5"></i>
                    <i class="mdi mdi-chevron-right fs-5"></i>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover odoo-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40px;"><input type="checkbox" class="form-check-input"></th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>UoM</th>
                            <th class="text-end" style="width: 150px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $p)
                            <tr wire:key="view-{{ $p->id }}-{{ $iteration }}">
                                <td><input type="checkbox" class="form-check-input"></td>
                                <td wire:click="edit('{{ $p->id }}')" style="cursor:pointer">{{ $p->product_code }}</td>
                                <td wire:click="edit('{{ $p->id }}')" style="cursor:pointer">{{ $p->product_name }}</td>
                                <td wire:click="edit('{{ $p->id }}')" style="cursor:pointer">{{ $p->productType ? $p->productType->product_type_name : '' }}</td>
                                <td wire:click="edit('{{ $p->id }}')" style="cursor:pointer">{{ $p->itemCategory ? $p->itemCategory->category_name : '' }}</td>
                                <td wire:click="edit('{{ $p->id }}')" style="cursor:pointer">{{ $p->uom ? $p->uom->uom_name : '' }}</td>
                                <td class="text-end">
                                    <span wire:click="edit('{{ $p->id }}')" class="action-text me-2">EDIT</span>
                                    <span wire:click="delete('{{ $p->id }}')" wire:confirm="Are you sure you want to delete this product?" class="text-danger" style="cursor:pointer"><i class="mdi mdi-delete"></i></span>
                                </td>
                            </tr>
                        @endforeach

                        @if(count($products) === 0)
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No products found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
