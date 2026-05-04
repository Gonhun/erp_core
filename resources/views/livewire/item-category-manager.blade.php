<div>
    <style>
        .odoo-table {
            border-bottom: 1px solid #dee2e6;
            table-layout: fixed;
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
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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
            font-size: 0.85rem;
        }
        .action-text:hover {
            color: #006e6b;
        }
        .inline-input, .inline-select {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 6px 10px;
            width: 100%;
            outline: none;
            font-size: 0.9rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .inline-input:focus, .inline-select:focus {
            border-color: #008784;
            box-shadow: 0 0 0 0.2rem rgba(0, 135, 132, 0.25);
        }
        .form-switch .form-check-input {
            cursor: pointer;
        }
        .category-row {
            background-color: #f1f3f5;
            font-weight: 600;
        }
        .sub-category-row {
            background-color: #ffffff;
        }
        .indent-col {
            padding-left: 45px !important;
        }
        .editing-row {
            background-color: #e7f5f5 !important;
            box-shadow: inset 0 0 0 2px #008784;
        }
        .account-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 4px;
            background-color: #f1f3f5;
            color: #495057;
            border: 1px solid #dee2e6;
            display: inline-block;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
    
    <div class="container-fluid py-3 bg-white">
        <div class="mb-4">
            <div class="d-flex align-items-center gap-2 text-muted mb-2">
                <a href="{{ route('inventory.home') }}" class="text-muted text-decoration-none hover-primary">Inventory</a>
                <span>/</span>
                <span class="text-dark fw-bold">Categories</span>
            </div>
            <h2 class="h3 mb-0">Item Categories & Sub Categories</h2>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex gap-2">
                <button wire:click="createNewCat" class="btn btn-new shadow-sm">
                    <i class="mdi mdi-plus me-1"></i> NEW CATEGORY
                </button>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <span class="input-group-text bg-white border-end-0"><i class="mdi mdi-magnify text-muted"></i></span>
                    <input type="text" class="form-control border-start-0" placeholder="Search categories...">
                </div>
                <div class="text-muted small">
                    <span class="fw-bold text-dark">{{ count($categories) }}</span> Categories
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table odoo-table mb-0 table-hover">
                    <thead>
                        <tr>
                            <th style="width: 30%;">Name</th>
                            <th style="width: 20%;">Income Account</th>
                            <th style="width: 20%;">Expense Account</th>
                            <th class="text-center" style="width: 10%;">Status</th>
                            <th class="text-end" style="width: 20%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($catIsCreating)
                        <tr class="editing-row" wire:key="cat-create-row-{{ $iteration }}">
                            <td>
                                <input type="text" wire:model="new_cat_name" class="inline-input" placeholder="Enter category name...">
                                @error('new_cat_name') <div class="text-danger x-small mt-1">{{ $message }}</div> @enderror
                            </td>
                            <td>
                                <select wire:model="new_cat_income_account" class="inline-select">
                                    <option value="">Select Income Account</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select wire:model="new_cat_expense_account" class="inline-select">
                                    <option value="">Select Expense Account</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input" type="checkbox" wire:model="new_cat_is_active">
                                </div>
                            </td>
                            <td class="text-end">
                                <button wire:click="saveNewCat" wire:confirm="Create this category?" class="btn btn-sm btn-new py-1">SAVE</button>
                                <button wire:click="cancelNewCat" class="btn btn-sm btn-light py-1 border">CANCEL</button>
                            </td>
                        </tr>
                        @endif

                        @foreach($categories as $cat)
                            <!-- Item Category Row -->
                            @if($catEditingId === $cat->id)
                                <tr class="editing-row" wire:key="cat-edit-{{ $cat->id }}-{{ $iteration }}">
                                    <td>
                                        <input type="text" wire:model="edit_cat_name" class="inline-input">
                                        @error('edit_cat_name') <div class="text-danger x-small mt-1">{{ $message }}</div> @enderror
                                    </td>
                                    <td>
                                        <select wire:model="edit_cat_income_account" class="inline-select">
                                            <option value="">Select Income Account</option>
                                            @foreach($accounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select wire:model="edit_cat_expense_account" class="inline-select">
                                            <option value="">Select Expense Account</option>
                                            @foreach($accounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" wire:model="edit_cat_is_active">
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <button wire:click="saveEditCat" wire:confirm="Save changes?" class="btn btn-sm btn-new py-1">SAVE</button>
                                        <button wire:click="cancelEditCat" class="btn btn-sm btn-light py-1 border">CANCEL</button>
                                    </td>
                                </tr>
                            @else
                                <tr class="category-row" wire:key="cat-view-{{ $cat->id }}-{{ $iteration }}">
                                    <td wire:click="editCat('{{ $cat->id }}')" style="cursor:pointer">
                                        <i class="mdi mdi-folder-table me-2 text-primary"></i> {{ $cat->category_name }}
                                    </td>
                                    <td>
                                        @if($cat->incomeAccount)
                                            <span class="account-badge" title="{{ $cat->incomeAccount->account_code }} - {{ $cat->incomeAccount->account_name }}">
                                                {{ $cat->incomeAccount->account_code }}
                                            </span>
                                        @else
                                            <span class="text-muted small">---</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($cat->expenseAccount)
                                            <span class="account-badge" title="{{ $cat->expenseAccount->account_code }} - {{ $cat->expenseAccount->account_name }}">
                                                {{ $cat->expenseAccount->account_code }}
                                            </span>
                                        @else
                                            <span class="text-muted small">---</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $cat->is_active ? 'bg-success' : 'bg-secondary' }} rounded-pill" style="font-size: 0.7rem;">
                                            {{ $cat->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button wire:click="createNewSub('{{ $cat->id }}')" class="btn btn-sm text-primary p-0 me-3" title="Add Sub-Category">
                                            <i class="mdi mdi-plus-circle-outline fs-5"></i>
                                        </button>
                                        <button wire:click="editCat('{{ $cat->id }}')" class="btn btn-sm text-dark p-0 me-3" title="Edit Category">
                                            <i class="mdi mdi-pencil-outline fs-5"></i>
                                        </button>
                                        <button wire:click="deleteCat('{{ $cat->id }}')" wire:confirm="Delete this category and all sub-categories?" class="btn btn-sm text-danger p-0" title="Delete Category">
                                            <i class="mdi mdi-delete-outline fs-5"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endif

                            <!-- Sub Item Categories -->
                            @foreach($cat->subCategories as $sub)
                                @if($subEditingId === $sub->id)
                                    <tr class="editing-row" wire:key="sub-edit-{{ $sub->id }}-{{ $iteration }}">
                                        <td class="indent-col">
                                            <input type="text" wire:model="edit_sub_name" class="inline-input">
                                            @error('edit_sub_name') <div class="text-danger x-small mt-1">{{ $message }}</div> @enderror
                                        </td>
                                        <td>
                                            <select wire:model="edit_sub_income_account" class="inline-select">
                                                <option value="">Select Income Account</option>
                                                @foreach($accounts as $acc)
                                                    <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select wire:model="edit_sub_expense_account" class="inline-select">
                                                <option value="">Select Expense Account</option>
                                                @foreach($accounts as $acc)
                                                    <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" wire:model="edit_sub_is_active">
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <button wire:click="saveEditSub" wire:confirm="Save changes?" class="btn btn-sm btn-new py-1">SAVE</button>
                                            <button wire:click="cancelEditSub" class="btn btn-sm btn-light py-1 border">CANCEL</button>
                                        </td>
                                    </tr>
                                @else
                                    <tr class="sub-category-row" wire:key="sub-view-{{ $sub->id }}-{{ $iteration }}">
                                        <td class="indent-col" wire:click="editSub('{{ $sub->id }}')" style="cursor:pointer">
                                            <i class="mdi mdi-subdirectory-arrow-right me-2 text-muted"></i> {{ $sub->sub_category_name }}
                                        </td>
                                        <td>
                                            @if($sub->incomeAccount)
                                                <span class="account-badge" title="{{ $sub->incomeAccount->account_code }} - {{ $sub->incomeAccount->account_name }}">
                                                    {{ $sub->incomeAccount->account_code }}
                                                </span>
                                            @else
                                                <span class="text-muted small">---</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($sub->expenseAccount)
                                                <span class="account-badge" title="{{ $sub->expenseAccount->account_code }} - {{ $sub->expenseAccount->account_name }}">
                                                    {{ $sub->expenseAccount->account_code }}
                                                </span>
                                            @else
                                                <span class="text-muted small">---</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block pointer-events-none">
                                                <input class="form-check-input" type="checkbox" disabled {{ $sub->is_active ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <button wire:click="editSub('{{ $sub->id }}')" class="btn btn-sm text-dark p-0 me-3" title="Edit Sub-Category">
                                                <i class="mdi mdi-pencil-outline fs-5"></i>
                                            </button>
                                            <button wire:click="deleteSub('{{ $sub->id }}')" wire:confirm="Delete this sub-category?" class="btn btn-sm text-danger p-0" title="Delete Sub-Category">
                                                <i class="mdi mdi-delete-outline fs-5"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach

                            <!-- Create Sub Category Row -->
                            @if($subIsCreating === $cat->id)
                                <tr class="editing-row" wire:key="sub-create-row-{{ $cat->id }}-{{ $iteration }}">
                                    <td class="indent-col">
                                        <input type="text" wire:model="new_sub_name" class="inline-input" placeholder="Enter sub-category name...">
                                        @error('new_sub_name') <div class="text-danger x-small mt-1">{{ $message }}</div> @enderror
                                    </td>
                                    <td>
                                        <select wire:model="new_sub_income_account" class="inline-select">
                                            <option value="">Select Income Account</option>
                                            @foreach($accounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select wire:model="new_sub_expense_account" class="inline-select">
                                            <option value="">Select Expense Account</option>
                                            @foreach($accounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" wire:model="new_sub_is_active">
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <button wire:click="saveNewSub" wire:confirm="Create this sub-category?" class="btn btn-sm btn-new py-1">SAVE</button>
                                        <button wire:click="cancelNewSub" class="btn btn-sm btn-light py-1 border">CANCEL</button>
                                    </td>
                                </tr>
                            @endif
                        @endforeach

                        @if(count($categories) === 0 && !$catIsCreating)
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-alert-circle-outline fs-1 d-block mb-2"></i>
                                    No categories found. Click "New Category" to get started.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
