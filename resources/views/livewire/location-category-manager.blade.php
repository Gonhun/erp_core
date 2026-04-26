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
        .inline-input {
            border: 1px solid #008784;
            border-radius: 2px;
            padding: 4px 8px;
            width: 100%;
            outline: none;
        }
    </style>
    
    <div class="container-fluid py-3 bg-white">
        <div class="mb-4">
            <a href="{{ route('inventory.home') }}" class="text-muted text-decoration-none">
                <i class="mdi mdi-arrow-left"></i> Back to Inventory
            </a>
            <h2 class="mt-2">Location Categories</h2>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <button wire:click="createNew" class="btn btn-new border-0 shadow-none me-2">NEW</button>
            </div>
            <div class="text-muted small d-flex align-items-center">
                <span class="me-3"><i class="mdi mdi-filter-variant"></i> Filters</span>
                <span class="me-3"><i class="mdi mdi-format-list-bulleted-type"></i> Group By</span>
                <span><i class="mdi mdi-star"></i> Favorites</span>
            </div>
            <div class="text-muted small">
                1-{{ count($categories) }} / {{ count($categories) }}
                <i class="mdi mdi-chevron-left ms-2 fs-5"></i>
                <i class="mdi mdi-chevron-right fs-5"></i>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover odoo-table mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" class="form-check-input"></th>
                        <th>Category Name</th>
                        <th class="text-end" style="width: 150px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @if($isCreating)
                    <tr class="table-info" wire:key="create-row-{{ $iteration }}">
                        <td></td>
                        <td>
                            <input type="text" wire:model="new_location_name" class="inline-input" placeholder="Category Name">
                        </td>
                        <td class="text-end">
                            <span wire:click="saveNew" wire:confirm="Create this category?" class="action-text me-2">SAVE</span>
                            <span wire:click="cancelNew" class="text-muted" style="cursor:pointer">DISCARD</span>
                        </td>
                    </tr>
                    @endif

                    @foreach($categories as $cat)
                        @if($editingId === $cat->id)
                            <tr wire:key="edit-{{ $cat->id }}-{{ $iteration }}">
                                <td><input type="checkbox" class="form-check-input"></td>
                                <td>
                                    <input type="text" wire:model="edit_location_name" class="inline-input">
                                </td>
                                <td class="text-end">
                                    <span wire:click="saveEdit" wire:confirm="Save changes?" class="action-text me-2">SAVE</span>
                                    <span wire:click="cancelEdit" class="text-muted" style="cursor:pointer">DISCARD</span>
                                </td>
                            </tr>
                        @else
                            <tr wire:key="view-{{ $cat->id }}-{{ $iteration }}">
                                <td><input type="checkbox" class="form-check-input"></td>
                                <td wire:click="edit('{{ $cat->id }}')" style="cursor:pointer">{{ $cat->location_name }}</td>
                                <td class="text-end">
                                    <span wire:click="edit('{{ $cat->id }}')" class="action-text me-2">EDIT</span>
                                    <span wire:click="delete('{{ $cat->id }}')" wire:confirm="Delete this category?" class="text-danger" style="cursor:pointer"><i class="mdi mdi-delete"></i></span>
                                </td>
                            </tr>
                        @endif
                    @endforeach

                    @if(count($categories) === 0 && !$isCreating)
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">
                                No categories found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
