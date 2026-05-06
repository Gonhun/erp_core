<div>
    <style>
        .odoo-table {
            border-bottom: 1px solid #dee2e6;
            width: 100%;
        }
        .odoo-table th {
            color: #495057;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            border-top: none;
            border-bottom: 2px solid #dee2e6;
            padding: 12px 16px;
            background-color: #f8f9fa;
        }
        .odoo-table td {
            vertical-align: middle;
            color: #212529;
            padding: 10px 16px;
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
        .inline-input {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 6px 10px;
            width: 100%;
            outline: none;
            font-size: 0.9rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .inline-input:focus {
            border-color: #008784;
            box-shadow: 0 0 0 0.2rem rgba(0, 135, 132, 0.25);
        }
        .editing-row {
            background-color: #e7f5f5 !important;
            box-shadow: inset 0 0 0 2px #008784;
        }
    </style>
    
    <div class="container-fluid py-3 bg-white">
        <div class="mb-4">
            <div class="d-flex align-items-center gap-2 text-muted mb-2">
                <a href="{{ route('inventory.home') }}" class="text-muted text-decoration-none small">Inventory</a>
                <span>/</span>
                <span class="text-dark fw-bold small">Units of Measure</span>
            </div>
            <h2 class="h3 mb-0">Units of Measure</h2>
        </div>

        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show py-2 small mx-0" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <button wire:click="createNew" class="btn btn-new shadow-sm">
                <i class="mdi mdi-plus me-1"></i> NEW UOM
            </button>
            <div class="text-muted small">
                <span class="fw-bold text-dark">{{ count($categories) }}</span> Units
            </div>
        </div>

        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table odoo-table mb-0 table-hover">
                    <thead>
                        <tr>
                            <th style="width: 40%;">Name (Base UoM Name)</th>
                            <th style="width: 25%;">Code (Base UoM)</th>
                            <th class="text-center" style="width: 15%;">Status</th>
                            <th class="text-end" style="width: 20%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($isCreating)
                            <tr class="editing-row" wire:key="create-row-{{ $iteration }}">
                                <td>
                                    <input type="text" wire:model="new_base_uom_name" class="inline-input" placeholder="e.g. Kilogram">
                                    @error('new_base_uom_name') <div class="text-danger small mt-1" style="font-size: 11px;">{{ $message }}</div> @enderror
                                </td>
                                <td>
                                    <input type="text" wire:model="new_base_uom" class="inline-input" placeholder="e.g. kg">
                                    @error('new_base_uom') <div class="text-danger small mt-1" style="font-size: 11px;">{{ $message }}</div> @enderror
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input" type="checkbox" wire:model="new_is_active">
                                    </div>
                                </td>
                                <td class="text-end">
                                    <button wire:click="saveNew" wire:confirm="Create this Unit of Measure?" class="btn btn-sm btn-new py-1">SAVE</button>
                                    <button wire:click="cancelCreate" class="btn btn-sm btn-light py-1 border">CANCEL</button>
                                </td>
                            </tr>
                        @endif

                        @foreach($categories as $c)
                            @if($editingId === $c->id)
                                <tr class="editing-row" wire:key="edit-row-{{ $c->id }}-{{ $iteration }}">
                                    <td>
                                        <input type="text" wire:model="edit_base_uom_name" class="inline-input">
                                        @error('edit_base_uom_name') <div class="text-danger small mt-1" style="font-size: 11px;">{{ $message }}</div> @enderror
                                    </td>
                                    <td>
                                        <input type="text" wire:model="edit_base_uom" class="inline-input">
                                        @error('edit_base_uom') <div class="text-danger small mt-1" style="font-size: 11px;">{{ $message }}</div> @enderror
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" wire:model="edit_is_active">
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <button wire:click="saveEdit" wire:confirm="Save changes?" class="btn btn-sm btn-new py-1">SAVE</button>
                                        <button wire:click="cancelEdit" class="btn btn-sm btn-light py-1 border">CANCEL</button>
                                    </td>
                                </tr>
                            @else
                                <tr wire:key="view-row-{{ $c->id }}-{{ $iteration }}">
                                    <td wire:click="edit('{{ $c->id }}')" style="cursor:pointer" class="fw-bold">
                                        <i class="mdi mdi-package-variant-closed me-2 text-primary"></i> {{ $c->base_uom_name }}
                                    </td>
                                    <td wire:click="edit('{{ $c->id }}')" style="cursor:pointer">
                                        <span class="badge bg-light text-dark border">{{ $c->base_uom }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $c->is_active ? 'bg-success' : 'bg-secondary' }} rounded-pill" style="font-size: 0.75rem;">
                                            {{ $c->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button wire:click="edit('{{ $c->id }}')" class="btn btn-sm text-dark p-0 me-3" title="Edit">
                                            <i class="mdi mdi-pencil-outline fs-5"></i>
                                        </button>
                                        <button wire:click="delete('{{ $c->id }}')" wire:confirm="Are you sure you want to delete this unit?" class="btn btn-sm text-danger p-0" title="Delete">
                                            <i class="mdi mdi-delete-outline fs-5"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        @endforeach

                        @if(count($categories) === 0 && !$isCreating)
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-information-outline fs-3 d-block mb-2"></i>
                                    No Units of Measure found. Click "NEW UOM" to add one.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
