<div>
    <style>
        .odoo-table { border-bottom: 1px solid #dee2e6; }
        .odoo-table th { color: #495057; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; border-top: none; border-bottom: 2px solid #dee2e6; padding: 12px 16px; background-color: #f8f9fa; }
        .odoo-table td { vertical-align: middle; color: #212529; padding: 10px 16px; border-bottom: 1px solid #e9ecef; }
        .btn-new { background-color: #008784; color: white; border-radius: 2px; font-weight: bold; padding: 6px 16px; }
        .btn-new:hover { background-color: #006e6b; color: white; }
        .action-text { color: #008784; font-weight: 600; text-decoration: none; cursor: pointer; font-size: 0.85rem; }
        .action-text:hover { color: #006e6b; }
        .custom-tab-bar button { cursor: pointer; transition: all 0.2s; }
        .form-label { font-size: 0.8rem; margin-bottom: 4px; color: #666; font-weight: 600; }
        .form-control, .form-select { border-radius: 2px; font-size: 0.9rem; border: 1px solid #ccc; }
        .form-control:focus, .form-select:focus { border-color: #008784; box-shadow: 0 0 0 0.2rem rgba(0, 135, 132, 0.15); }
    </style>

    <div class="container-fluid py-3 bg-white">
        <div class="mb-4">
            <a href="{{ route('purchasing.home') }}" class="text-muted text-decoration-none small">
                <i class="mdi mdi-arrow-left"></i> Back to Purchasing
            </a>
            <h2 class="mt-2 fw-bold" style="color: #333;">Suppliers</h2>
        </div>

        @if($isCreating || $editingId)
            <div class="card border-0 shadow-sm" style="border: 1px solid #dee2e6 !important;" wire:key="supplier-form-{{ $iteration }}">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold">{{ $isCreating ? 'New Supplier' : 'Edit Supplier' }}</h5>
                </div>
                <div class="card-body p-0">
                    @if ($errors->any())
                        <div class="alert alert-danger mx-4 mt-3 mb-0 py-2">
                            <ul class="mb-0 small">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                        </div>
                    @endif

                    <div class="d-flex border-bottom px-4 custom-tab-bar" style="background-color: #f8f9fa;">
                        @foreach(['general' => 'General Info', 'accounting' => 'Accounting', 'tax' => 'Tax Details', 'bank' => 'Bank Accounts', 'contacts' => 'Contacts'] as $key => $label)
                            <button type="button" wire:click="setTab('{{ $key }}')" 
                                class="py-3 px-4 fw-bold border-0 bg-transparent {{ $activeTab == $key ? 'text-primary border-bottom border-primary border-3' : 'text-muted' }}"
                                style="{{ $activeTab == $key ? 'border-bottom: 3px solid #008784 !important; color: #008784 !important;' : '' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>

                    <div class="p-4">
                        @if($activeTab == 'general')
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Supplier Code</label>
                                    <input type="text" class="form-control" wire:model="supplier_code" placeholder="Auto-generated (SUP-XXXX)">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Supplier Name</label>
                                    <input type="text" class="form-control" wire:model="supplier_name" placeholder="e.g. Acme Corp">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" rows="2" wire:model="supplier_address"></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Region/City</label>
                                    <input type="text" class="form-control" wire:model="supplier_region">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">State/Province</label>
                                    <input type="text" class="form-control" wire:model="supplier_state">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Postal Code</label>
                                    <input type="text" class="form-control" wire:model="supplier_postal_code">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Phone</label>
                                    <input type="text" class="form-control" wire:model="supplier_phone">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" wire:model="supplier_email">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Website</label>
                                    <input type="text" class="form-control" wire:model="supplier_website">
                                </div>
                                <div class="col-md-4 mt-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" wire:model="is_active" id="supActive">
                                        <label class="form-check-label fw-bold" for="supActive">Active</label>
                                    </div>
                                </div>
                            </div>
                        @elseif($activeTab == 'accounting')
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Account Receivable</label>
                                    <select class="form-select" wire:model="account_receivable">
                                        <option value="">Select Account...</option>
                                        @foreach($accounts as $acc) <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option> @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Account Payable</label>
                                    <select class="form-select" wire:model="account_payable">
                                        <option value="">Select Account...</option>
                                        @foreach($accounts as $acc) <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option> @endforeach
                                    </select>
                                </div>
                            </div>
                        @elseif($activeTab == 'tax')
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">NPWP</label>
                                    <input type="text" class="form-control" wire:model="supplier_npwp">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">NIK (Individual)</label>
                                    <input type="text" class="form-control" wire:model="supplier_nik">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Tax Legal Name</label>
                                    <input type="text" class="form-control" wire:model="tax_name">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Tax Address</label>
                                    <textarea class="form-control" rows="2" wire:model="tax_address"></textarea>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" wire:model="is_pkp" id="isPkp">
                                        <label class="form-check-label fw-bold" for="isPkp">Is PKP</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" wire:model="is_ppn" id="isPpn">
                                        <label class="form-check-label fw-bold" for="isPpn">Is PPN</label>
                                    </div>
                                </div>
                            </div>
                        @elseif($activeTab == 'bank')
                            <div class="table-responsive">
                                <table class="table table-bordered odoo-table shadow-none">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Bank Name</th>
                                            <th>Account Name</th>
                                            <th>Account Number</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bank_accounts as $index => $bank)
                                            <tr wire:key="bank-row-{{ $index }}">
                                                <td><input type="text" class="form-control border-0 shadow-none" wire:model="bank_accounts.{{ $index }}.bank"></td>
                                                <td><input type="text" class="form-control border-0 shadow-none" wire:model="bank_accounts.{{ $index }}.bank_account_name"></td>
                                                <td><input type="text" class="form-control border-0 shadow-none" wire:model="bank_accounts.{{ $index }}.bank_account_number"></td>
                                                <td class="text-center">
                                                    <button class="btn btn-link text-danger p-0 mt-1" wire:click="removeBankRow({{ $index }})"><i class="mdi mdi-delete-outline fs-5"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <button class="btn btn-link action-text text-decoration-none p-0" wire:click="addBankRow"><i class="mdi mdi-plus-circle-outline"></i> Add a bank account</button>
                            </div>
                        @elseif($activeTab == 'contacts')
                            <div class="table-responsive">
                                <table class="table table-bordered odoo-table shadow-none">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 150px;">Type</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone/Mobile</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($contacts as $index => $contact)
                                            <tr wire:key="contact-row-{{ $index }}">
                                                <td>
                                                    <select class="form-select border-0 shadow-none" wire:model="contacts.{{ $index }}.contact_type">
                                                        <option value="Contact Person">Contact Person</option>
                                                        <option value="Billing">Billing</option>
                                                        <option value="Shipping">Shipping</option>
                                                        <option value="Other">Other</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control border-0 shadow-none" wire:model="contacts.{{ $index }}.contact_name"></td>
                                                <td><input type="email" class="form-control border-0 shadow-none" wire:model="contacts.{{ $index }}.supplier_contact_email"></td>
                                                <td><input type="text" class="form-control border-0 shadow-none" wire:model="contacts.{{ $index }}.supplier_contact_mobile"></td>
                                                <td class="text-center">
                                                    <button class="btn btn-link text-danger p-0 mt-1" wire:click="removeContactRow({{ $index }})"><i class="mdi mdi-delete-outline fs-5"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <button class="btn btn-link action-text text-decoration-none p-0" wire:click="addContactRow"><i class="mdi mdi-plus-circle-outline"></i> Add a contact</button>
                            </div>
                        @endif

                        <div class="mt-4 pt-3 border-top d-flex gap-2">
                            <button wire:click="save" wire:confirm="Are you sure you want to save this supplier?" class="btn btn-new">SAVE</button>
                            <button wire:click="cancel" class="btn btn-light border">DISCARD</button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="d-flex justify-content-between align-items-center mb-4">
                <button wire:click="createNew" class="btn btn-new shadow-sm"><i class="mdi mdi-plus me-1"></i> NEW SUPPLIER</button>
                <div class="text-muted small"><span class="fw-bold text-dark">{{ count($suppliers) }}</span> Suppliers</div>
            </div>

            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table odoo-table mb-0 table-hover">
                        <thead>
                            <tr>
                                <th style="width: 15%;">Code</th>
                                <th style="width: 35%;">Supplier Name</th>
                                <th style="width: 25%;">Phone/Email</th>
                                <th class="text-center" style="width: 10%;">Status</th>
                                <th class="text-end" style="width: 15%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suppliers as $s)
                                <tr wire:key="sup-{{ $s->id }}-{{ $iteration }}">
                                    <td wire:click="edit('{{ $s->id }}')" style="cursor:pointer" class="fw-bold text-primary">{{ $s->supplier_code }}</td>
                                    <td wire:click="edit('{{ $s->id }}')" style="cursor:pointer">{{ $s->supplier_name }}</td>
                                    <td wire:click="edit('{{ $s->id }}')" style="cursor:pointer">
                                        <div class="small">{{ $s->supplier_phone }}</div>
                                        <div class="text-muted x-small">{{ $s->supplier_email }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $s->is_active ? 'bg-success' : 'bg-secondary' }} rounded-pill" style="font-size: 0.7rem;">
                                            {{ $s->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span wire:click="edit('{{ $s->id }}')" class="action-text me-3">EDIT</span>
                                        <span wire:click="delete('{{ $s->id }}')" wire:confirm="Delete this supplier?" class="text-danger" style="cursor:pointer"><i class="mdi mdi-delete-outline fs-5"></i></span>
                                    </td>
                                </tr>
                            @endforeach
                            @if(count($suppliers) == 0)
                                <tr><td colspan="5" class="text-center py-5 text-muted">No suppliers found. Click "NEW SUPPLIER" to add one.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
