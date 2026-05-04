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
        .custom-tab-bar button {
            cursor: pointer;
            transition: all 0.2s;
        }
    </style>
    
    <div class="container-fluid py-3 bg-white">
        <div class="mb-4">
            <a href="{{ route('finance.home') }}" class="text-muted text-decoration-none">
                <i class="mdi mdi-arrow-left"></i> Back to Finance
            </a>
            <h2 class="mt-2">Customers</h2>
        </div>

        @if($isCreating || $editingId)
            <!-- Form View -->
            <div class="card mb-4 border-0 shadow-sm" style="border: 1px solid #dee2e6 !important;" wire:key="form-card-{{ $iteration }}">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0">{{ $isCreating ? 'New Customer' : 'Edit Customer' }}</h5>
                </div>
                <div class="card-body p-0">
                    @if ($errors->any())
                        <div class="alert alert-danger mx-4 mt-3 mb-0 py-2">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- ERP Style Tabs -->
                    <div class="d-flex border-bottom px-4 custom-tab-bar" style="background-color: #f8f9fa;">
                        @foreach(['general' => 'General Info', 'accounting' => 'Accounting', 'tax' => 'Tax Details', 'bank' => 'Bank Accounts', 'contacts' => 'Contacts'] as $key => $label)
                            <button type="button" 
                                wire:click="setTab('{{ $key }}')" 
                                class="py-3 px-4 fw-bold border-0 bg-transparent {{ $activeTab == $key ? 'text-primary border-bottom border-primary border-3' : 'text-muted' }}"
                                style="{{ $activeTab == $key ? 'border-bottom: 3px solid #008784 !important; color: #008784 !important;' : '' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>

                    <div class="p-4">
                        @if($activeTab == 'general')
                            <div class="row g-4">
                                <div class="col-md-3">
                                    <label class="form-label text-muted small fw-bold">Customer Code</label>
                                    <input type="text" class="form-control bg-light" wire:model="customer_code" readonly>
                                    @error('customer_code') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">Customer Name</label>
                                    <input type="text" class="form-control" wire:model="customer_name">
                                    @error('customer_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-switch mt-4">
                                        <input class="form-check-input" type="checkbox" wire:model="is_active" id="custActive">
                                        <label class="form-check-label fw-bold" for="custActive">Active</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">Address</label>
                                    <textarea class="form-control" rows="3" wire:model="customer_address"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label text-muted small fw-bold">Region / City</label>
                                            <input type="text" class="form-control" wire:model="customer_region">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted small fw-bold">State / Province</label>
                                            <input type="text" class="form-control" wire:model="customer_state">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted small fw-bold">Postal Code</label>
                                            <input type="text" class="form-control" wire:model="customer_postal_code">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label text-muted small fw-bold">Phone</label>
                                    <input type="text" class="form-control" wire:model="customer_phone">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-muted small fw-bold">Mobile</label>
                                    <input type="text" class="form-control" wire:model="customer_mobile">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-muted small fw-bold">Email</label>
                                    <input type="email" class="form-control" wire:model="customer_email">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-muted small fw-bold">Website</label>
                                    <input type="text" class="form-control" wire:model="customer_website">
                                </div>

                                <div class="col-md-3">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" wire:model="is_individual" id="isInd">
                                        <label class="form-check-label" for="isInd">Individual</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" wire:model="is_company" id="isComp">
                                        <label class="form-check-label" for="isComp">Company</label>
                                    </div>
                                </div>
                            </div>

                        @elseif($activeTab == 'accounting')
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">Account Receivable</label>
                                    <select class="form-select" wire:model="account_receivable">
                                        <option value="">Select Account...</option>
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('account_receivable') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">Account Payable</label>
                                    <select class="form-select" wire:model="account_payable">
                                        <option value="">Select Account...</option>
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('account_payable') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-bold">Credit Limit</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">Rp</span>
                                        <input type="number" class="form-control" wire:model="credit_limit">
                                    </div>
                                </div>
                            </div>

                        @elseif($activeTab == 'tax')
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-bold">NPWP</label>
                                    <input type="text" class="form-control" wire:model="customer_npwp">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-bold">NIK (Indonesian ID)</label>
                                    <input type="text" class="form-control" wire:model="customer_nik">
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex gap-4 mt-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" wire:model="is_ppn" id="isPPN">
                                            <label class="form-check-label" for="isPPN">PPN</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" wire:model="is_pkp" id="isPKP">
                                            <label class="form-check-label" for="isPKP">PKP</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">Legal Tax Name (NPWP Name)</label>
                                    <input type="text" class="form-control" wire:model="tax_name">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label text-muted small fw-bold">Legal Tax Address (NPWP Address)</label>
                                    <textarea class="form-control" rows="2" wire:model="tax_address"></textarea>
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
                                            <th style="width: 100px;" class="text-center">Active</th>
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
                                                    <div class="form-check form-switch d-inline-block mt-2">
                                                        <input class="form-check-input" type="checkbox" wire:model="bank_accounts.{{ $index }}.is_active">
                                                    </div>
                                                </td>
                                                <td>
                                                    <button class="btn btn-link text-danger p-0 mt-2" wire:click="removeBankRow({{ $index }})">
                                                        <i class="mdi mdi-delete-outline fs-5"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <button class="btn btn-link action-text text-decoration-none p-0" wire:click="addBankRow">
                                    <i class="mdi mdi-plus-circle-outline"></i> Add a bank account
                                </button>
                            </div>

                        @elseif($activeTab == 'contacts')
                            <div class="table-responsive">
                                <table class="table table-bordered odoo-table shadow-none">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Type</th>
                                            <th>Email</th>
                                            <th>Phone/Mobile</th>
                                            <th>Address</th>
                                            <th style="width: 100px;" class="text-center">Active</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($contacts as $index => $contact)
                                            <tr wire:key="contact-row-{{ $index }}">
                                                <td>
                                                    <select class="form-select border-0 shadow-none" wire:model="contacts.{{ $index }}.contact_type">
                                                        <option value="Billing">Billing</option>
                                                        <option value="Shipping">Shipping</option>
                                                        <option value="Other">Other</option>
                                                    </select>
                                                </td>
                                                <td><input type="email" class="form-control border-0 shadow-none" wire:model="contacts.{{ $index }}.customer_contact_email"></td>
                                                <td>
                                                    <input type="text" class="form-control border-0 shadow-none mb-1" placeholder="Phone" wire:model="contacts.{{ $index }}.customer_contact_phone">
                                                    <input type="text" class="form-control border-0 shadow-none" placeholder="Mobile" wire:model="contacts.{{ $index }}.customer_contact_mobile">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control border-0 shadow-none" placeholder="Address" wire:model="contacts.{{ $index }}.customer_contact_address">
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check form-switch d-inline-block mt-2">
                                                        <input class="form-check-input" type="checkbox" wire:model="contacts.{{ $index }}.is_active">
                                                    </div>
                                                </td>
                                                <td>
                                                    <button class="btn btn-link text-danger p-0 mt-2" wire:click="removeContactRow({{ $index }})">
                                                        <i class="mdi mdi-delete-outline fs-5"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <button class="btn btn-link action-text text-decoration-none p-0" wire:click="addContactRow">
                                    <i class="mdi mdi-plus-circle-outline"></i> Add a contact
                                </button>
                            </div>
                        @endif

                        <div class="mt-4 pt-3 border-top d-flex gap-2">
                            <button wire:click="save" wire:confirm="Are you sure you want to save this customer?" class="btn btn-new">SAVE</button>
                            <button wire:click="cancel" class="btn btn-light border">DISCARD</button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- List View -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <button wire:click="createNew" class="btn btn-new border-0 shadow-none me-2">NEW CUSTOMER</button>
                </div>
                <div class="text-muted small d-flex align-items-center">
                    <span class="me-3"><i class="mdi mdi-filter-variant"></i> Filters</span>
                    <span class="me-3"><i class="mdi mdi-format-list-bulleted-type"></i> Group By</span>
                    <span><i class="mdi mdi-star"></i> Favorites</span>
                </div>
                <div class="text-muted small">
                    1-{{ count($customers) }} / {{ count($customers) }}
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
                            <th>Region</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th class="text-end" style="width: 150px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $c)
                            <tr wire:key="view-{{ $c->id }}-{{ $iteration }}">
                                <td><input type="checkbox" class="form-check-input"></td>
                                <td wire:click="edit('{{ $c->id }}')" style="cursor:pointer">{{ $c->customer_code }}</td>
                                <td wire:click="edit('{{ $c->id }}')" style="cursor:pointer">{{ $c->customer_name }}</td>
                                <td wire:click="edit('{{ $c->id }}')" style="cursor:pointer">{{ $c->customer_region }}</td>
                                <td wire:click="edit('{{ $c->id }}')" style="cursor:pointer">{{ $c->customer_phone }}</td>
                                <td wire:click="edit('{{ $c->id }}')" style="cursor:pointer">
                                    <span class="badge {{ $c->is_active ? 'bg-success' : 'bg-secondary' }} rounded-pill">
                                        {{ $c->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <span wire:click="edit('{{ $c->id }}')" class="action-text me-2">EDIT</span>
                                    <span wire:click="delete('{{ $c->id }}')" wire:confirm="Are you sure you want to delete this customer?" class="text-danger" style="cursor:pointer"><i class="mdi mdi-delete"></i></span>
                                </td>
                            </tr>
                        @endforeach

                        @if(count($customers) === 0)
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No customers found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
