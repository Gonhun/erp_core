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
        .inline-input, .inline-select {
            border: 1px solid #008784;
            border-radius: 2px;
            padding: 4px 8px;
            width: 100%;
            outline: none;
        }
        .form-switch .form-check-input {
            cursor: pointer;
        }
    </style>
    
    <div class="container-fluid py-3 bg-white">
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
                1-{{ count($accounts) }} / {{ count($accounts) }}
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
                        <th>Account Name</th>
                        <th>Group Account</th>
                        <th>Type</th>
                        <th class="text-center">Reconciliation</th>
                        <th class="text-center">Active</th>
                        <th>Currency</th>
                        <th class="text-end" style="width: 150px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Inline Create Row -->
                    @if($isCreating)
                    <tr class="table-info" wire:key="create-row-{{ $iteration }}">
                        <td></td>
                        <td>
                            <input type="text" wire:model="new_account_code" class="inline-input" placeholder="Code">
                        </td>
                        <td>
                            <input type="text" wire:model="new_account_name" class="inline-input" placeholder="Name">
                        </td>
                        <td>
                            <select x-data="{ model: @entangle('new_group_account_id') }" x-init="$($el).select2({ placeholder: 'Select Group...', width: '100%' }); $($el).on('change', function(){ model = $($el).val() }); $watch('model', value => { $($el).val(value).trigger('change.select2') });" class="inline-select">
                                <option value=""></option>
                                @foreach($groupAccounts as $group)
                                    <option value="{{ $group->id }}">{{ $group->group_accounts_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select wire:model="new_balance_type" class="inline-select">
                                <option value="">Select Type...</option>
                                <option value="Debit">Debit</option>
                                <option value="Credit">Credit</option>
                            </select>
                        </td>
                        <td class="text-center">
                            <div class="form-check form-switch d-inline-block">
                                <input class="form-check-input" type="checkbox" wire:model="new_is_reconciliation">
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="form-check form-switch d-inline-block">
                                <input class="form-check-input" type="checkbox" wire:model="new_is_active">
                            </div>
                        </td>
                        <td>
                            <select x-data="{ model: @entangle('new_account_currency') }" x-init="$($el).select2({ placeholder: 'Currency...', width: '100%', allowClear: true }); $($el).on('change', function(){ model = $($el).val() }); $watch('model', value => { $($el).val(value).trigger('change.select2') });" class="inline-select">
                                <option value="">-- No Currency --</option>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency }}">{{ $currency }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-end">
                            <span wire:click="saveNew" wire:confirm="Are you sure you want to create this account?" class="action-text me-2">SAVE</span>
                            <span wire:click="cancelNew" class="text-muted" style="cursor:pointer">DISCARD</span>
                        </td>
                    </tr>
                    @endif

                    <!-- Data Rows -->
                    @foreach($accounts as $account)
                        @if($editingId === $account->id)
                            <tr wire:key="edit-{{ $account->id }}-{{ $iteration }}">
                                <td><input type="checkbox" class="form-check-input"></td>
                                <!-- Inline Edit Row -->
                                <td><input type="text" wire:model="edit_account_code" class="inline-input"></td>
                                <td><input type="text" wire:model="edit_account_name" class="inline-input"></td>
                                <td>
                                    <select x-data="{ model: @entangle('edit_group_account_id') }" x-init="$($el).select2({ placeholder: 'Select Group...', width: '100%' }); $($el).on('change', function(){ model = $($el).val() }); $watch('model', value => { $($el).val(value).trigger('change.select2') });" class="inline-select">
                                        <option value=""></option>
                                        @foreach($groupAccounts as $group)
                                            <option value="{{ $group->id }}">{{ $group->group_accounts_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select wire:model="edit_balance_type" class="inline-select">
                                        <option value="Debit">Debit</option>
                                        <option value="Credit">Credit</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input" type="checkbox" wire:model="edit_is_reconciliation">
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input" type="checkbox" wire:model="edit_is_active">
                                    </div>
                                </td>
                                <td>
                                    <select x-data="{ model: @entangle('edit_account_currency') }" x-init="$($el).select2({ placeholder: 'Currency...', width: '100%', allowClear: true }); $($el).on('change', function(){ model = $($el).val() }); $watch('model', value => { $($el).val(value).trigger('change.select2') });" class="inline-select">
                                        <option value="">-- No Currency --</option>
                                        @foreach($currencies as $currency)
                                            <option value="{{ $currency }}">{{ $currency }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-end">
                                    <span wire:click="saveEdit" wire:confirm="Are you sure you want to save these changes?" class="action-text me-2">SAVE</span>
                                    <span wire:click="cancelEdit" class="text-muted" style="cursor:pointer">DISCARD</span>
                                </td>
                            </tr>
                        @else
                            <tr wire:key="view-{{ $account->id }}-{{ $iteration }}">
                                <td><input type="checkbox" class="form-check-input"></td>
                                <!-- Normal Row -->
                                <td wire:click="edit('{{ $account->id }}')" style="cursor:pointer">{{ $account->account_code }}</td>
                                <td wire:click="edit('{{ $account->id }}')" style="cursor:pointer">{{ $account->account_name }}</td>
                                <td wire:click="edit('{{ $account->id }}')" style="cursor:pointer">{{ $account->groupAccount ? $account->groupAccount->group_accounts_name : '' }}</td>
                                <td wire:click="edit('{{ $account->id }}')" style="cursor:pointer">{{ $account->balance_type }}</td>
                                <td wire:click="edit('{{ $account->id }}')" class="text-center" style="cursor:pointer">
                                    <div class="form-check form-switch d-inline-block pointer-events-none">
                                        <input class="form-check-input" type="checkbox" disabled {{ $account->is_reconciliation ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td wire:click="edit('{{ $account->id }}')" class="text-center" style="cursor:pointer">
                                    <div class="form-check form-switch d-inline-block pointer-events-none">
                                        <input class="form-check-input" type="checkbox" disabled {{ $account->is_active ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td wire:click="edit('{{ $account->id }}')" style="cursor:pointer">{{ $account->account_currency }}</td>
                                <td class="text-end">
                                    <span wire:click="edit('{{ $account->id }}')" class="action-text me-2">EDIT</span>
                                    <span wire:click="delete('{{ $account->id }}')" wire:confirm="Are you sure you want to delete this account?" class="text-danger" style="cursor:pointer"><i class="mdi mdi-delete"></i></span>
                                </td>
                            </tr>
                        @endif
                    @endforeach

                    @if(count($accounts) === 0 && !$isCreating)
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                No accounts found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
