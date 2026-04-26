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
    </style>
    
    <div class="container-fluid py-3 bg-white">
        <div class="mb-4">
            <a href="{{ route('inventory.home') }}" class="text-muted text-decoration-none">
                <i class="mdi mdi-arrow-left"></i> Back to Inventory
            </a>
            <h2 class="mt-2">Warehouses</h2>
        </div>

        @if($isCreating || $editingId)
            <!-- Form View -->
            <div class="card mb-4 border-0 shadow-sm" style="border: 1px solid #dee2e6 !important;">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0">{{ $isCreating ? 'New Warehouse' : 'Edit Warehouse' }}</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Warehouse Code</label>
                            <input type="text" class="form-control bg-light" wire:model="{{ $isCreating ? 'new_warehouse_code' : 'edit_warehouse_code' }}" {{ $isCreating ? 'readonly' : '' }}>
                            @error($isCreating ? 'new_warehouse_code' : 'edit_warehouse_code') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Warehouse Name</label>
                            <input type="text" class="form-control" wire:model="{{ $isCreating ? 'new_warehouse_name' : 'edit_warehouse_name' }}">
                            @error($isCreating ? 'new_warehouse_name' : 'edit_warehouse_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Short Name</label>
                            <input type="text" class="form-control" maxlength="15" wire:model="{{ $isCreating ? 'new_warehouse_short_name' : 'edit_warehouse_short_name' }}">
                            @error($isCreating ? 'new_warehouse_short_name' : 'edit_warehouse_short_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Address / Note</label>
                            <textarea class="form-control" rows="3" wire:model="{{ $isCreating ? 'new_warehouse_address' : 'edit_warehouse_address' }}"></textarea>
                            @error($isCreating ? 'new_warehouse_address' : 'edit_warehouse_address') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">Shipment Type</label>
                                    <select class="form-select" wire:model="{{ $isCreating ? 'new_shipment_type' : 'edit_shipment_type' }}">
                                        <option value="">Select...</option>
                                        <option value="1 Step">1 Step</option>
                                        <option value="2 Step">2 Step</option>
                                        <option value="3 Step">3 Step</option>
                                    </select>
                                    @error($isCreating ? 'new_shipment_type' : 'edit_shipment_type') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="col-6 mt-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="buyResupply" wire:model="{{ $isCreating ? 'new_is_buy_resupply' : 'edit_is_buy_resupply' }}">
                                        <label class="form-check-label" for="buyResupply">Buy to Resupply</label>
                                    </div>
                                </div>
                                <div class="col-6 mt-4">
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
                            <button wire:click="saveNew" wire:confirm="Are you sure you want to create this warehouse?" class="btn btn-new">SAVE</button>
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
                    <button wire:click="createNew" class="btn btn-new border-0 shadow-none me-2">NEW WAREHOUSE</button>
                </div>
                <div class="text-muted small d-flex align-items-center">
                    <span class="me-3"><i class="mdi mdi-filter-variant"></i> Filters</span>
                    <span class="me-3"><i class="mdi mdi-format-list-bulleted-type"></i> Group By</span>
                    <span><i class="mdi mdi-star"></i> Favorites</span>
                </div>
                <div class="text-muted small">
                    1-{{ count($warehouses) }} / {{ count($warehouses) }}
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
                            <th>Short Name</th>
                            <th>Shipment Type</th>
                            <th class="text-center">Resupply</th>
                            <th class="text-center">Active</th>
                            <th class="text-end" style="width: 150px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($warehouses as $wh)
                            <tr wire:key="view-{{ $wh->id }}-{{ $iteration }}">
                                <td><input type="checkbox" class="form-check-input"></td>
                                <td wire:click="edit('{{ $wh->id }}')" style="cursor:pointer">{{ $wh->warehouse_code }}</td>
                                <td wire:click="edit('{{ $wh->id }}')" style="cursor:pointer">{{ $wh->warehouse_name }}</td>
                                <td wire:click="edit('{{ $wh->id }}')" style="cursor:pointer">{{ $wh->warehouse_short_name }}</td>
                                <td wire:click="edit('{{ $wh->id }}')" style="cursor:pointer">{{ $wh->shipment_type }}</td>
                                <td wire:click="edit('{{ $wh->id }}')" class="text-center" style="cursor:pointer">
                                    <div class="form-check form-switch d-inline-block pointer-events-none">
                                        <input class="form-check-input" type="checkbox" disabled {{ $wh->is_buy_resupply ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td wire:click="edit('{{ $wh->id }}')" class="text-center" style="cursor:pointer">
                                    <div class="form-check form-switch d-inline-block pointer-events-none">
                                        <input class="form-check-input" type="checkbox" disabled {{ $wh->is_active ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span wire:click="edit('{{ $wh->id }}')" class="action-text me-2">EDIT</span>
                                    <span wire:click="delete('{{ $wh->id }}')" wire:confirm="Are you sure you want to delete this warehouse?" class="text-danger" style="cursor:pointer"><i class="mdi mdi-delete"></i></span>
                                </td>
                            </tr>
                        @endforeach

                        @if(count($warehouses) === 0)
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    No warehouses found.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
