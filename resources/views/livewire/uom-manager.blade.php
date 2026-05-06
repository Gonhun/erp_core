<div>
    <style>
        .odoo-table {
            border-bottom: 1px solid #dee2e6;
        }
        .odoo-table th {
            color: #495057;
            font-weight: 600;
            font-size: 0.85rem;
            border-top: none;
            border-bottom: 2px solid #dee2e6;
            padding: 10px 16px;
            text-transform: uppercase;
        }
        .odoo-table td {
            vertical-align: middle;
            color: #212529;
            padding: 8px 16px;
            border-bottom: 1px solid #e9ecef;
            font-size: 0.95rem;
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
        .category-header {
            background-color: #f8f9fa;
            border-left: 4px solid #008784;
            padding: 10px 20px;
            margin-top: 2rem;
            margin-bottom: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
    
    <div class="container-fluid py-3 bg-white">
        <div class="mb-4">
            <a href="{{ route('inventory.home') }}" class="text-muted text-decoration-none">
                <i class="mdi mdi-arrow-left"></i> Back to Inventory
            </a>
            <h2 class="mt-2">Units of Measure</h2>
        </div>

        @if($isCreating || $editingId)
            <!-- Form View -->
            <div class="card mb-4 border-0 shadow-sm" style="border: 1px solid #dee2e6 !important;" wire:key="form-card-{{ $iteration }}">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0">{{ $isCreating ? 'New UoM' : 'Edit UoM' }}</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">UoM Name</label>
                            <input type="text" class="form-control" wire:model="uom_name">
                            @error('uom_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Category</label>
                            <select class="form-select" wire:model="uom_category_id">
                                <option value="">Select Category...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                @endforeach
                            </select>
                            @error('uom_category_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Type</label>
                            <select class="form-select" wire:model="uom_type">
                                <option value="Reference">Reference Unit of Measure for this category</option>
                                <option value="Smaller">Smaller than the reference Unit of Measure</option>
                                <option value="Bigger">Bigger than the reference Unit of Measure</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Ratio</label>
                            <input type="number" step="0.0001" class="form-control" wire:model="ratio">
                            @error('ratio') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" wire:model="is_active" id="uomActive">
                                <label class="form-check-label fw-bold" for="uomActive">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex gap-2">
                        <button wire:click="save" wire:confirm="Are you sure you want to save this UoM?" class="btn btn-new">SAVE</button>
                        <button wire:click="cancel" class="btn btn-light border">DISCARD</button>
                    </div>
                </div>
            </div>
        @else
            <!-- List View (Detail Based on Category) -->
            <div class="mb-3">
                <button wire:click="createNew" class="btn btn-new border-0 shadow-none">NEW UOM</button>
            </div>

            @foreach($categories as $cat)
                <div class="category-header shadow-sm rounded-top border">
                    <div class="d-flex align-items-center">
                        <i class="mdi mdi-package-variant-closed me-2 text-primary"></i>
                        <h5 class="mb-0 fw-bold">{{ $cat->category_name }}</h5>
                        <span class="ms-3 badge bg-light text-dark border fw-normal">Base: {{ $cat->base_uom_name }} ({{ $cat->base_uom }})</span>
                    </div>
                    <button wire:click="createNew('{{ $cat->id }}')" class="btn btn-sm btn-link action-text text-decoration-none fw-bold">
                        <i class="mdi mdi-plus"></i> Add UoM
                    </button>
                </div>
                <div class="table-responsive mb-5">
                    <table class="table table-hover odoo-table border-start border-end border-bottom">
                        <thead>
                            <tr class="bg-white">
                                <th>Unit of Measure</th>
                                <th>Type</th>
                                <th>Ratio</th>
                                <th class="text-center" style="width: 100px;">Status</th>
                                <th class="text-end" style="width: 150px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cat->uoms as $uom)
                                <tr wire:key="uom-{{ $uom->id }}-{{ $iteration }}">
                                    <td class="fw-bold">{{ $uom->uom_name }}</td>
                                    <td>
                                        @if($uom->uom_type == 'Reference')
                                            <span class="text-primary fw-bold">Reference</span>
                                        @elseif($uom->uom_type == 'Smaller')
                                            <span class="text-warning">Smaller</span>
                                        @else
                                            <span class="text-info">Bigger</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($uom->uom_type == 'Reference')
                                            1.0000
                                        @else
                                            {{ number_format($uom->ratio, 4) }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $uom->is_active ? 'bg-success' : 'bg-secondary' }} rounded-pill">
                                            {{ $uom->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span wire:click="edit('{{ $uom->id }}')" class="action-text me-2">EDIT</span>
                                        <span wire:click="delete('{{ $uom->id }}')" wire:confirm="Are you sure you want to delete this UoM?" class="text-danger" style="cursor:pointer"><i class="mdi mdi-delete"></i></span>
                                    </td>
                                </tr>
                            @endforeach
                            @if($cat->uoms->isEmpty())
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted small italic">No units defined in this category.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            @endforeach
        @endif
    </div>
</div>
