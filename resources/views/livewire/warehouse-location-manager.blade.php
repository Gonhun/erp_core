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
        .form-switch .form-check-input:checked {
            background-color: #008784;
            border-color: #008784;
        }
        .select2-container--bootstrap-5 .select2-selection {
            border: 1px solid #dee2e6;
        }
    </style>
    
    <div class="container-fluid py-3 bg-white">
        <div class="mb-4">
            <a href="{{ route('inventory.home') }}" class="text-muted text-decoration-none">
                <i class="mdi mdi-arrow-left"></i> Back to Inventory
            </a>
            <h2 class="mt-2">Warehouse Locations</h2>
        </div>

        @if($isCreating || $editingId)
            <!-- Form View -->
            <div class="card mb-4 border-0 shadow-sm" style="border: 1px solid #dee2e6 !important;" wire:key="form-card-{{ $iteration }}">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0">{{ $isCreating ? 'New Location' : 'Edit Location' }}</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Location Code</label>
                            <input type="text" class="form-control bg-light" wire:model="{{ $isCreating ? 'new_location_code' : 'edit_location_code' }}" {{ $isCreating ? 'readonly' : '' }}>
                            @error($isCreating ? 'new_location_code' : 'edit_location_code') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Location Name</label>
                            <input type="text" class="form-control" wire:model="{{ $isCreating ? 'new_location_name' : 'edit_location_name' }}">
                            @error($isCreating ? 'new_location_name' : 'edit_location_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Warehouse</label>
                            <div wire:ignore>
                                <select x-data="{ model: @entangle(($isCreating ? 'new_warehouse_id' : 'edit_warehouse_id')) }" x-init="$($el).select2({ theme: 'bootstrap-5', placeholder: 'Select Warehouse...', width: '100%' }); $($el).on('change', function(){ model = $($el).val() }); $watch('model', value => { $($el).val(value).trigger('change.select2') });" class="form-select">
                                    <option value=""></option>
                                    @foreach($warehouses as $wh)
                                        <option value="{{ $wh->id }}">{{ $wh->warehouse_code }} - {{ $wh->warehouse_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error($isCreating ? 'new_warehouse_id' : 'edit_warehouse_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Location Category</label>
                            <div wire:ignore>
                                <select x-data="{ model: @entangle(($isCreating ? 'new_location_type' : 'edit_location_type')) }" x-init="$($el).select2({ theme: 'bootstrap-5', placeholder: 'Select Category...', width: '100%' }); $($el).on('change', function(){ model = $($el).val() }); $watch('model', value => { $($el).val(value).trigger('change.select2') });" class="form-select">
                                    <option value=""></option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->location_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error($isCreating ? 'new_location_type' : 'edit_location_type') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Parent Location</label>
                            <div wire:ignore>
                                <select x-data="{ model: @entangle(($isCreating ? 'new_parent_location' : 'edit_parent_location')) }" x-init="$($el).select2({ theme: 'bootstrap-5', placeholder: 'Select Parent (Optional)...', width: '100%', allowClear: true }); $($el).on('change', function(){ model = $($el).val() }); $watch('model', value => { $($el).val(value).trigger('change.select2') });" class="form-select">
                                    <option value=""></option>
                                    @foreach($parentLocations as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->location_code }} - {{ $parent->location_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error($isCreating ? 'new_parent_location' : 'edit_parent_location') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Inventory Frequency (Days)</label>
                            <input type="number" class="form-control" wire:model="{{ $isCreating ? 'new_inventory_frequency' : 'edit_inventory_frequency' }}">
                            @error($isCreating ? 'new_inventory_frequency' : 'edit_inventory_frequency') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label text-muted small fw-bold">Location Note</label>
                            <textarea class="form-control" rows="3" wire:model="{{ $isCreating ? 'new_location_note' : 'edit_location_note' }}"></textarea>
                            @error($isCreating ? 'new_location_note' : 'edit_location_note') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-12 mt-4 pt-3 border-top">
                            <h6 class="text-muted mb-3">Location Configuration</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="isParent" wire:model="{{ $isCreating ? 'new_is_parent' : 'edit_is_parent' }}">
                                        <label class="form-check-label" for="isParent">Is Parent Location</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="isNegativeStock" wire:model="{{ $isCreating ? 'new_is_negative_stock' : 'edit_is_negative_stock' }}">
                                        <label class="form-check-label" for="isNegativeStock">Allow Negative Stock</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="isScrapLocation" wire:model="{{ $isCreating ? 'new_is_scrap_location' : 'edit_is_scrap_location' }}">
                                        <label class="form-check-label" for="isScrapLocation">Is Scrap Location</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="isReturnLocation" wire:model="{{ $isCreating ? 'new_is_return_location' : 'edit_is_return_location' }}">
                                        <label class="form-check-label" for="isReturnLocation">Is Return Location</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="isReplenish" wire:model="{{ $isCreating ? 'new_is_replenish' : 'edit_is_replenish' }}">
                                        <label class="form-check-label" for="isReplenish">Is Replenish</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="isActive" wire:model="{{ $isCreating ? 'new_is_active' : 'edit_is_active' }}">
                                        <label class="form-check-label" for="isActive">Is Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="mt-4 pt-3 border-top d-flex gap-2">
                        @if($isCreating)
                            <button wire:click="saveNew" wire:confirm="Are you sure you want to create this location?" class="btn btn-new">SAVE</button>
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
                    <button wire:click="createNew" class="btn btn-new border-0 shadow-none me-2">NEW LOCATION</button>
                </div>
                <div class="text-muted small d-flex align-items-center">
                    <span class="me-3"><i class="mdi mdi-filter-variant"></i> Filters</span>
                    <span class="me-3"><i class="mdi mdi-format-list-bulleted-type"></i> Group By</span>
                    <span><i class="mdi mdi-star"></i> Favorites</span>
                </div>
                <div class="text-muted small">
                    1-{{ count($locations) }} / {{ count($locations) }}
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
                            <th>Warehouse</th>
                            <th>Category</th>
                            <th class="text-center">Parent</th>
                            <th class="text-center">Active</th>
                            <th class="text-end" style="width: 150px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locations as $loc)
                            <tr wire:key="view-{{ $loc->id }}-{{ $iteration }}">
                                <td><input type="checkbox" class="form-check-input"></td>
                                <td wire:click="edit('{{ $loc->id }}')" style="cursor:pointer">{{ $loc->location_code }}</td>
                                <td wire:click="edit('{{ $loc->id }}')" style="cursor:pointer">{{ $loc->location_name }}</td>
                                <td wire:click="edit('{{ $loc->id }}')" style="cursor:pointer">{{ $loc->warehouse ? $loc->warehouse->warehouse_name : '' }}</td>
                                <td wire:click="edit('{{ $loc->id }}')" style="cursor:pointer">{{ $loc->category ? $loc->category->location_name : '' }}</td>
                                <td wire:click="edit('{{ $loc->id }}')" class="text-center" style="cursor:pointer">
                                    <div class="form-check form-switch d-inline-block pointer-events-none">
                                        <input class="form-check-input" type="checkbox" disabled {{ $loc->is_parent ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td wire:click="edit('{{ $loc->id }}')" class="text-center" style="cursor:pointer">
                                    <div class="form-check form-switch d-inline-block pointer-events-none">
                                        <input class="form-check-input" type="checkbox" disabled {{ $loc->is_active ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span wire:click="edit('{{ $loc->id }}')" class="action-text me-2">EDIT</span>
                                    <span wire:click="delete('{{ $loc->id }}')" wire:confirm="Are you sure you want to delete this location?" class="text-danger" style="cursor:pointer"><i class="mdi mdi-delete"></i></span>
                                </td>
                            </tr>
                        @endforeach

                        @if(count($locations) === 0)
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    No locations found.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
