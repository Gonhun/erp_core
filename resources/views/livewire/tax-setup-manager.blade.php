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
        .nav-tabs .nav-link {
            color: #495057;
            border: none;
            border-bottom: 2px solid transparent;
            font-weight: 500;
        }
        .nav-tabs .nav-link.active {
            color: #008784;
            border-bottom: 2px solid #008784;
            background: none;
        }
    </style>
    
    <div class="container-fluid py-3">
        <div class="mb-4">
            <a href="{{ route('finance.taxes') }}" class="text-muted text-decoration-none">
                <i class="mdi mdi-arrow-left"></i> Back to Taxes
            </a>
            <h2 class="mt-2">{{ $tax->tax_name }} Setup</h2>
        </div>

        <div class="bg-white shadow-sm p-4 rounded">
            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'definition' ? 'active' : '' }}" href="#" wire:click.prevent="setTab('definition')">Definition</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'setup' ? 'active' : '' }}" href="#" wire:click.prevent="setTab('setup')">Advanced Options</a>
                </li>
            </ul>

            <!-- Tab Content -->
            @if($activeTab === 'definition')
                <div>
                    <!-- Invoice Definitions -->
                    <div class="mb-5">
                        <h5 class="mb-3">Invoice Definitions</h5>
                        <div class="d-flex mb-3">
                            <button wire:click="createNewInv" class="btn btn-new border-0 shadow-none me-2">NEW INVOICE DEF</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover odoo-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Account</th>
                                        <th>Amount</th>
                                        <th class="text-end" style="width: 150px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($invIsCreating)
                                    <tr class="table-info" wire:key="inv-create-row-{{ $invIteration }}">
                                        <td>
                                            <select x-data="{ model: @entangle('new_inv_account_id') }" x-init="$($el).select2({ placeholder: 'Select Account...', width: '100%' }); $($el).on('change', function(){ model = $($el).val() }); $watch('model', value => { $($el).val(value).trigger('change.select2') });" class="inline-select">
                                                <option value=""></option>
                                                @foreach($accounts as $acc)
                                                    <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" wire:model="new_inv_def_amount" class="inline-input">
                                        </td>
                                        <td class="text-end">
                                            <span wire:click="saveNewInv" wire:confirm="Create invoice definition?" class="action-text me-2">SAVE</span>
                                            <span wire:click="cancelNewInv" class="text-muted" style="cursor:pointer">DISCARD</span>
                                        </td>
                                    </tr>
                                    @endif

                                    @foreach($tax->invoiceDefinitions as $def)
                                        @if($invEditingId === $def->id)
                                            <tr wire:key="inv-edit-{{ $def->id }}-{{ $invIteration }}">
                                                <td>
                                                    <select x-data="{ model: @entangle('edit_inv_account_id') }" x-init="$($el).select2({ placeholder: 'Select Account...', width: '100%' }); $($el).on('change', function(){ model = $($el).val() }); $watch('model', value => { $($el).val(value).trigger('change.select2') });" class="inline-select">
                                                        <option value=""></option>
                                                        @foreach($accounts as $acc)
                                                            <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" wire:model="edit_inv_def_amount" class="inline-input">
                                                </td>
                                                <td class="text-end">
                                                    <span wire:click="saveEditInv" wire:confirm="Save changes?" class="action-text me-2">SAVE</span>
                                                    <span wire:click="cancelEditInv" class="text-muted" style="cursor:pointer">DISCARD</span>
                                                </td>
                                            </tr>
                                        @else
                                            <tr wire:key="inv-view-{{ $def->id }}-{{ $invIteration }}">
                                                <td wire:click="editInv('{{ $def->id }}')" style="cursor:pointer">{{ $def->account ? $def->account->account_code . ' - ' . $def->account->account_name : '' }}</td>
                                                <td wire:click="editInv('{{ $def->id }}')" style="cursor:pointer">{{ number_format($def->def_amount, 2) }}</td>
                                                <td class="text-end">
                                                    <span wire:click="editInv('{{ $def->id }}')" class="action-text me-2">EDIT</span>
                                                    <span wire:click="deleteInv('{{ $def->id }}')" wire:confirm="Delete this definition?" class="text-danger" style="cursor:pointer"><i class="mdi mdi-delete"></i></span>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach

                                    @if(count($tax->invoiceDefinitions) === 0 && !$invIsCreating)
                                        <tr><td colspan="3" class="text-center py-4 text-muted">No invoice definitions found.</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Refund Definitions -->
                    <div>
                        <h5 class="mb-3">Refund Definitions</h5>
                        <div class="d-flex mb-3">
                            <button wire:click="createNewRef" class="btn btn-new border-0 shadow-none me-2">NEW REFUND DEF</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover odoo-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Account</th>
                                        <th>Amount</th>
                                        <th class="text-end" style="width: 150px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($refIsCreating)
                                    <tr class="table-info" wire:key="ref-create-row-{{ $refIteration }}">
                                        <td>
                                            <select x-data="{ model: @entangle('new_ref_account_id') }" x-init="$($el).select2({ placeholder: 'Select Account...', width: '100%' }); $($el).on('change', function(){ model = $($el).val() }); $watch('model', value => { $($el).val(value).trigger('change.select2') });" class="inline-select">
                                                <option value=""></option>
                                                @foreach($accounts as $acc)
                                                    <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" wire:model="new_ref_def_amount" class="inline-input">
                                        </td>
                                        <td class="text-end">
                                            <span wire:click="saveNewRef" wire:confirm="Create refund definition?" class="action-text me-2">SAVE</span>
                                            <span wire:click="cancelNewRef" class="text-muted" style="cursor:pointer">DISCARD</span>
                                        </td>
                                    </tr>
                                    @endif

                                    @foreach($tax->refundDefinitions as $def)
                                        @if($refEditingId === $def->id)
                                            <tr wire:key="ref-edit-{{ $def->id }}-{{ $refIteration }}">
                                                <td>
                                                    <select x-data="{ model: @entangle('edit_ref_account_id') }" x-init="$($el).select2({ placeholder: 'Select Account...', width: '100%' }); $($el).on('change', function(){ model = $($el).val() }); $watch('model', value => { $($el).val(value).trigger('change.select2') });" class="inline-select">
                                                        <option value=""></option>
                                                        @foreach($accounts as $acc)
                                                            <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" wire:model="edit_ref_def_amount" class="inline-input">
                                                </td>
                                                <td class="text-end">
                                                    <span wire:click="saveEditRef" wire:confirm="Save changes?" class="action-text me-2">SAVE</span>
                                                    <span wire:click="cancelEditRef" class="text-muted" style="cursor:pointer">DISCARD</span>
                                                </td>
                                            </tr>
                                        @else
                                            <tr wire:key="ref-view-{{ $def->id }}-{{ $refIteration }}">
                                                <td wire:click="editRef('{{ $def->id }}')" style="cursor:pointer">{{ $def->account ? $def->account->account_code . ' - ' . $def->account->account_name : '' }}</td>
                                                <td wire:click="editRef('{{ $def->id }}')" style="cursor:pointer">{{ number_format($def->def_amount, 2) }}</td>
                                                <td class="text-end">
                                                    <span wire:click="editRef('{{ $def->id }}')" class="action-text me-2">EDIT</span>
                                                    <span wire:click="deleteRef('{{ $def->id }}')" wire:confirm="Delete this definition?" class="text-danger" style="cursor:pointer"><i class="mdi mdi-delete"></i></span>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach

                                    @if(count($tax->refundDefinitions) === 0 && !$refIsCreating)
                                        <tr><td colspan="3" class="text-center py-4 text-muted">No refund definitions found.</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            @if($activeTab === 'setup')
                <div>
                    <h5 class="mb-3">Tax Setup Details</h5>
                    <div class="d-flex mb-3">
                        <button wire:click="createNewSetup" class="btn btn-new border-0 shadow-none me-2">NEW SETUP</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover odoo-table mb-0">
                            <thead>
                                <tr>
                                    <th>Tax Label</th>
                                    <th>Country</th>
                                    <th class="text-center">Included in Price</th>
                                    <th class="text-end" style="width: 150px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($setupIsCreating)
                                <tr class="table-info" wire:key="setup-create-row-{{ $setupIteration }}">
                                    <td>
                                        <input type="text" wire:model="new_setup_tax_label" class="inline-input">
                                    </td>
                                    <td>
                                        <input type="text" wire:model="new_setup_tax_country" class="inline-input">
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" wire:model="new_setup_is_included_price">
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span wire:click="saveNewSetup" wire:confirm="Create tax setup?" class="action-text me-2">SAVE</span>
                                        <span wire:click="cancelNewSetup" class="text-muted" style="cursor:pointer">DISCARD</span>
                                    </td>
                                </tr>
                                @endif

                                @foreach($tax->setups as $setup)
                                    @if($setupEditingId === $setup->id)
                                        <tr wire:key="setup-edit-{{ $setup->id }}-{{ $setupIteration }}">
                                            <td>
                                                <input type="text" wire:model="edit_setup_tax_label" class="inline-input">
                                            </td>
                                            <td>
                                                <input type="text" wire:model="edit_setup_tax_country" class="inline-input">
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-switch d-inline-block">
                                                    <input class="form-check-input" type="checkbox" wire:model="edit_setup_is_included_price">
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <span wire:click="saveEditSetup" wire:confirm="Save changes?" class="action-text me-2">SAVE</span>
                                                <span wire:click="cancelEditSetup" class="text-muted" style="cursor:pointer">DISCARD</span>
                                            </td>
                                        </tr>
                                    @else
                                        <tr wire:key="setup-view-{{ $setup->id }}-{{ $setupIteration }}">
                                            <td wire:click="editSetup('{{ $setup->id }}')" style="cursor:pointer">{{ $setup->tax_label }}</td>
                                            <td wire:click="editSetup('{{ $setup->id }}')" style="cursor:pointer">{{ $setup->tax_country }}</td>
                                            <td wire:click="editSetup('{{ $setup->id }}')" class="text-center" style="cursor:pointer">
                                                <div class="form-check form-switch d-inline-block pointer-events-none">
                                                    <input class="form-check-input" type="checkbox" disabled {{ $setup->is_included_price ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <span wire:click="editSetup('{{ $setup->id }}')" class="action-text me-2">EDIT</span>
                                                <span wire:click="deleteSetup('{{ $setup->id }}')" wire:confirm="Delete this setup?" class="text-danger" style="cursor:pointer"><i class="mdi mdi-delete"></i></span>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach

                                @if(count($tax->setups) === 0 && !$setupIsCreating)
                                    <tr><td colspan="4" class="text-center py-4 text-muted">No setup configurations found.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
