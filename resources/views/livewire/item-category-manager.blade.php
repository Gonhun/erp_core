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
        .form-switch .form-check-input {
            cursor: pointer;
        }
        .category-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .sub-category-row td:first-child {
            padding-left: 40px;
        }
        .indent-col {
            padding-left: 30px !important;
        }
    </style>
    
    <div class="container-fluid py-3 bg-white">
        <div class="mb-4">
            <a href="{{ route('inventory.home') }}" class="text-muted text-decoration-none">
                <i class="mdi mdi-arrow-left"></i> Back to Inventory
            </a>
            <h2 class="mt-2">Item Categories & Sub Categories</h2>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <button wire:click="createNewCat" class="btn btn-new border-0 shadow-none me-2">NEW CATEGORY</button>
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
                        <th>Category / Sub Category Name</th>
                        <th class="text-center" style="width: 100px;">Active</th>
                        <th class="text-end" style="width: 250px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @if($catIsCreating)
                    <tr class="table-info" wire:key="cat-create-row-{{ $iteration }}">
                        <td>
                            <input type="text" wire:model="new_cat_name" class="inline-input" placeholder="Category Name">
                            @error('new_cat_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </td>
                        <td class="text-center">
                            <div class="form-check form-switch d-inline-block">
                                <input class="form-check-input" type="checkbox" wire:model="new_cat_is_active">
                            </div>
                        </td>
                        <td class="text-end">
                            <span wire:click="saveNewCat" wire:confirm="Are you sure you want to create this category?" class="action-text me-2">SAVE</span>
                            <span wire:click="cancelNewCat" class="text-muted" style="cursor:pointer">DISCARD</span>
                        </td>
                    </tr>
                    @endif

                    @foreach($categories as $cat)
                        <!-- Item Category Row -->
                        @if($catEditingId === $cat->id)
                            <tr class="category-row" wire:key="cat-edit-{{ $cat->id }}-{{ $iteration }}">
                                <td>
                                    <input type="text" wire:model="edit_cat_name" class="inline-input">
                                    @error('edit_cat_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input" type="checkbox" wire:model="edit_cat_is_active">
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span wire:click="saveEditCat" wire:confirm="Are you sure you want to save these changes?" class="action-text me-2">SAVE</span>
                                    <span wire:click="cancelEditCat" class="text-muted" style="cursor:pointer">DISCARD</span>
                                </td>
                            </tr>
                        @else
                            <tr class="category-row" wire:key="cat-view-{{ $cat->id }}-{{ $iteration }}">
                                <td wire:click="editCat('{{ $cat->id }}')" style="cursor:pointer">
                                    <i class="mdi mdi-folder-outline me-2 text-warning"></i> {{ $cat->category_name }}
                                </td>
                                <td wire:click="editCat('{{ $cat->id }}')" class="text-center" style="cursor:pointer">
                                    <div class="form-check form-switch d-inline-block pointer-events-none">
                                        <input class="form-check-input" type="checkbox" disabled {{ $cat->is_active ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span wire:click="createNewSub('{{ $cat->id }}')" class="action-text text-primary me-2"><i class="mdi mdi-plus-circle-outline"></i> ADD SUB</span>
                                    <span wire:click="editCat('{{ $cat->id }}')" class="action-text me-2">EDIT</span>
                                    <span wire:click="deleteCat('{{ $cat->id }}')" wire:confirm="Are you sure you want to delete this category and all its subcategories?" class="text-danger" style="cursor:pointer"><i class="mdi mdi-delete"></i></span>
                                </td>
                            </tr>
                        @endif

                        <!-- Sub Item Categories -->
                        @foreach($cat->subCategories as $sub)
                            @if($subEditingId === $sub->id)
                                <tr class="sub-category-row" wire:key="sub-edit-{{ $sub->id }}-{{ $iteration }}">
                                    <td class="indent-col">
                                        <input type="text" wire:model="edit_sub_name" class="inline-input">
                                        @error('edit_sub_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" wire:model="edit_sub_is_active">
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span wire:click="saveEditSub" wire:confirm="Are you sure you want to save these changes?" class="action-text me-2">SAVE</span>
                                        <span wire:click="cancelEditSub" class="text-muted" style="cursor:pointer">DISCARD</span>
                                    </td>
                                </tr>
                            @else
                                <tr class="sub-category-row" wire:key="sub-view-{{ $sub->id }}-{{ $iteration }}">
                                    <td class="indent-col" wire:click="editSub('{{ $sub->id }}')" style="cursor:pointer">
                                        <i class="mdi mdi-subdirectory-arrow-right me-2 text-muted"></i> {{ $sub->sub_category_name }}
                                    </td>
                                    <td wire:click="editSub('{{ $sub->id }}')" class="text-center" style="cursor:pointer">
                                        <div class="form-check form-switch d-inline-block pointer-events-none">
                                            <input class="form-check-input" type="checkbox" disabled {{ $sub->is_active ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span wire:click="editSub('{{ $sub->id }}')" class="action-text me-2">EDIT</span>
                                        <span wire:click="deleteSub('{{ $sub->id }}')" wire:confirm="Are you sure you want to delete this subcategory?" class="text-danger" style="cursor:pointer"><i class="mdi mdi-delete"></i></span>
                                    </td>
                                </tr>
                            @endif
                        @endforeach

                        <!-- Create Sub Category Row -->
                        @if($subIsCreating === $cat->id)
                            <tr class="table-info sub-category-row" wire:key="sub-create-row-{{ $cat->id }}-{{ $iteration }}">
                                <td class="indent-col">
                                    <input type="text" wire:model="new_sub_name" class="inline-input" placeholder="Sub Category Name">
                                    @error('new_sub_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input" type="checkbox" wire:model="new_sub_is_active">
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span wire:click="saveNewSub" wire:confirm="Are you sure you want to create this subcategory?" class="action-text me-2">SAVE</span>
                                    <span wire:click="cancelNewSub" class="text-muted" style="cursor:pointer">DISCARD</span>
                                </td>
                            </tr>
                        @endif
                    @endforeach

                    @if(count($categories) === 0 && !$catIsCreating)
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
