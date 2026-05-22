<div>
    <style>
        .odoo-table th { color: #495057; font-weight: 600; font-size: 0.85rem; border-bottom: 2px solid #dee2e6; padding: 12px 16px; }
        .odoo-table td { vertical-align: middle; padding: 8px 16px; border-bottom: 1px solid #eee; font-size: 0.9rem; }
        .btn-validate { background-color: #008784; color: white; font-weight: bold; }
        .btn-validate:hover { background-color: #006e6b; color: white; }
        .status-badge { font-size: 0.75rem; padding: 4px 10px; border-radius: 20px; font-weight: bold; text-transform: uppercase; }
        .bg-draft { background-color: #e9ecef; color: #495057; }
        .bg-posted { background-color: #cce5ff; color: #004085; }
        .bg-paid { background-color: #d4edda; color: #155724; }
        .bg-cancel { background-color: #f8d7da; color: #721c24; }
        .form-label { font-size: 0.8rem; margin-bottom: 4px; color: #666; font-weight: 600; }
        .form-control, .form-select { border-radius: 2px; font-size: 0.9rem; border: 1px solid #ccc; }
        .form-control:focus, .form-select:focus { border-color: #008784; box-shadow: 0 0 0 0.2rem rgba(0, 135, 132, 0.15); }
    </style>

    <div class="container-fluid py-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Vendor Bills (AP)</h2>
        </div>

        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h5 class="fw-bold mb-2">Please correct the following errors:</h5>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($isCreating || $editingBillId)
            <!-- Form View -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2">
                        @if($status === 'draft')
                            <button wire:click="save" wire:confirm="Are you sure you want to save this draft?" class="btn btn-sm btn-validate px-3 shadow-sm">SAVE</button>
                            @if($editingBillId)
                                <button wire:click="postBill" wire:confirm="Are you sure you want to POST this bill? This will generate double-entry GL ledger items!" class="btn btn-sm btn-primary px-3 shadow-sm">POST JOURNAL</button>
                            @endif
                        @endif

                        @if($status === 'posted')
                            <button wire:click="cancelBill" wire:confirm="Are you sure you want to cancel this bill? This will reverse accounting entries!" class="btn btn-sm btn-danger px-3 shadow-sm">CANCEL BILL</button>
                        @endif

                        <button wire:click="discard" class="btn btn-sm btn-light border px-3">DISCARD</button>
                    </div>
                    <div class="text-muted small">
                        Number: <strong>{{ $bill_number }}</strong>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h1 class="h3 fw-bold text-muted">{{ $bill_number }}</h1>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="status-badge bg-{{ $status }}">{{ $status }}</span>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Vendor / Supplier</label>
                            @if($status !== 'draft')
                                <div class="fs-6 fw-bold">{{ $suppliersList->firstWhere('id', $supplier_id)->supplier_name ?? '-' }}</div>
                            @else
                                <div wire:ignore wire:key="supplier-wrapper-{{ $iteration }}">
                                    <select x-data="{ model: @entangle('supplier_id').live }" x-init="$($el).select2({ theme: 'bootstrap-5', placeholder: 'Select Vendor...', width: '100%' }); $($el).on('change', function(){ model = $($el).val() }); $watch('model', value => { $($el).val(value).trigger('change.select2') });" class="form-select">
                                        <option value=""></option>
                                        @foreach($suppliersList as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Purchase Order (Optional)</label>
                            @if($status !== 'draft')
                                <div class="fs-6">{{ $poList->firstWhere('id', $purchase_order_id)->po_number ?? 'Direct Invoice' }}</div>
                            @else
                                <div wire:ignore wire:key="po-wrapper-{{ $iteration }}">
                                    <select x-data="{ model: @entangle('purchase_order_id').live }" x-init="$($el).select2({ theme: 'bootstrap-5', placeholder: 'Direct Invoice (No PO)', width: '100%' }); $($el).on('change', function(){ model = $($el).val() }); $watch('model', value => { $($el).val(value).trigger('change.select2') });" class="form-select" {{ empty($supplier_id) ? 'disabled' : '' }}>
                                        <option value=""></option>
                                        @foreach($poList as $po)
                                            <option value="{{ $po->id }}">{{ $po->po_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Vendor Bill / Invoice Number</label>
                            @if($status !== 'draft')
                                <div class="fs-6">{{ $vendor_bill_number ?: '-' }}</div>
                            @else
                                <input type="text" class="form-control" wire:model="vendor_bill_number" placeholder="Vendor invoice number (e.g. INV-00213)">
                            @endif
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Bill Date</label>
                            @if($status !== 'draft')
                                <div class="fs-6">{{ $bill_date }}</div>
                            @else
                                <input type="date" class="form-control" wire:model.live="bill_date">
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Due Date</label>
                            @if($status !== 'draft')
                                <div class="fs-6">{{ $due_date ?: '-' }}</div>
                            @else
                                <input type="date" class="form-control" wire:model="due_date">
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Currency</label>
                            @if($status !== 'draft')
                                <div class="fs-6">{{ $currency }}</div>
                            @else
                                <input type="text" class="form-control" wire:model="currency" placeholder="IDR">
                            @endif
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Internal Notes / Terms</label>
                            @if($status !== 'draft')
                                <div class="fs-6">{{ $notes ?: '-' }}</div>
                            @else
                                <textarea class="form-control" wire:model="notes" rows="1" placeholder="Payment terms or notes..."></textarea>
                            @endif
                        </div>
                    </div>

                    <!-- Tabs Navs -->
                    <ul class="nav nav-tabs mb-4" id="billTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if($activeTab === 'lines') active fw-bold text-primary border-bottom border-primary border-3 @else text-secondary @endif" style="border:none; background:transparent;" wire:click="selectTab('lines')" type="button">Invoice Lines</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if($activeTab === 'journal') active fw-bold text-primary border-bottom border-primary border-3 @else text-secondary @endif" style="border:none; background:transparent;" wire:click="selectTab('journal')" type="button">Journal Items</button>
                        </li>
                    </ul>

                    @if($activeTab === 'lines')
                        <div class="table-responsive mb-4">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>GL Account</th>
                                        <th class="text-end" style="width: 120px;">Qty</th>
                                        <th class="text-end" style="width: 150px;">Unit Price</th>
                                        <th class="text-end" style="width: 100px;">Disc (%)</th>
                                        <th>Tax</th>
                                        <th class="text-end" style="width: 150px;">Subtotal</th>
                                        @if($status === 'draft')
                                            <th style="width: 50px;"></th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $index => $item)
                                        <tr wire:key="item-{{ $index }}">
                                            <td>
                                                @if($status !== 'draft')
                                                    <span class="fw-bold">{{ $item['product_name'] }}</span>
                                                @else
                                                    <select class="form-select form-select-sm" wire:model.live="items.{{ $index }}.product_id">
                                                        <option value="">Select Product...</option>
                                                        @foreach(\App\Models\Product::where('is_active', true)->get() as $p)
                                                            <option value="{{ $p->id }}">{{ $p->product_code }} - {{ $p->product_name }}</option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </td>
                                            <td>
                                                @if($status !== 'draft')
                                                    <span class="small text-muted">{{ $accountsList->firstWhere('id', $item['account_id'])->account_code ?? '' }} - {{ $accountsList->firstWhere('id', $item['account_id'])->account_name ?? '' }}</span>
                                                @else
                                                    <select class="form-select form-select-sm" wire:model.live="items.{{ $index }}.account_id">
                                                        @foreach($accountsList as $acc)
                                                            <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if($status !== 'draft')
                                                    {{ number_format($item['quantity'], 2) }}
                                                @else
                                                    <input type="number" step="0.01" class="form-control form-control-sm text-end" wire:model.live="items.{{ $index }}.quantity">
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if($status !== 'draft')
                                                    {{ number_format($item['unit_price'], 2) }}
                                                @else
                                                    <input type="number" wire:key="items-price-{{ $iteration }}" step="0.01" class="form-control form-control-sm text-end" wire:model.live="items.{{ $index }}.unit_price">
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if($status !== 'draft')
                                                    {{ number_format($item['discount'], 2) }}%
                                                @else
                                                    <input type="number" wire:key="items-discount-{{ $iteration }}" step="0.01" class="form-control form-control-sm text-end" wire:model.live="items.{{ $index }}.discount">
                                                @endif
                                            </td>
                                            <td>
                                                @if($status !== 'draft')
                                                    <span class="small">{{ $taxesList->firstWhere('id', $item['tax_id'])->tax_name ?? 'No Tax' }}</span>
                                                @else
                                                    <select class="form-select form-select-sm" wire:model.live="items.{{ $index }}.tax_id">
                                                        <option value="">No Tax</option>
                                                        @foreach($taxesList as $tax)
                                                            <option value="{{ $tax->id }}">{{ $tax->tax_name }}</option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </td>
                                            <td class="text-end fw-bold">
                                                {{ number_format($item['subtotal'], 2) }}
                                            </td>
                                            @if($status === 'draft')
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-link text-danger p-0" wire:click="removeRow({{ $index }})"><i class="mdi mdi-delete-outline fs-5"></i></button>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($status === 'draft')
                            <button class="btn btn-sm btn-outline-secondary mb-4" wire:click="addRow"><i class="mdi mdi-plus me-1"></i> Add line</button>
                        @endif
                    @endif

                    @if($activeTab === 'journal')
                        <div class="table-responsive mb-4">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Account</th>
                                        <th>Label</th>
                                        <th class="text-end" style="width: 200px;">Debit</th>
                                        <th class="text-end" style="width: 200px;">Credit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $journalItems = $this->getJournalItems();
                                        $totalDebit = 0;
                                        $totalCredit = 0;
                                    @endphp
                                    @foreach($journalItems as $jItem)
                                        @php
                                            $totalDebit += floatval($jItem['debit']);
                                            $totalCredit += floatval($jItem['credit']);
                                        @endphp
                                        <tr>
                                            <td class="fw-bold">
                                                <span class="text-primary">{{ $jItem['account_code'] }}</span>
                                                <span class="text-muted ms-2">{{ $jItem['account_name'] }}</span>
                                            </td>
                                            <td>{{ $jItem['label'] }}</td>
                                            <td class="text-end fw-bold text-success">
                                                @if(floatval($jItem['debit']) > 0)
                                                    {{ number_format($jItem['debit'], 2) }} {{ $currency }}
                                                @else
                                                    0.00
                                                @endif
                                            </td>
                                            <td class="text-end fw-bold text-danger">
                                                @if(floatval($jItem['credit']) > 0)
                                                    {{ number_format($jItem['credit'], 2) }} {{ $currency }}
                                                @else
                                                    0.00
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if(count($journalItems) === 0)
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted small">No journal items to display. Add lines first.</td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot class="table-light fw-bold border-top-2">
                                    <tr>
                                        <td colspan="2" class="text-end">Total:</td>
                                        <td class="text-end text-success fs-6">{{ number_format($totalDebit, 2) }} {{ $currency }}</td>
                                        <td class="text-end text-danger fs-6">{{ number_format($totalCredit, 2) }} {{ $currency }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif

                    <!-- Totals Block -->
                    <div class="row justify-content-end">
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Untaxed Amount:</span>
                                <span class="fw-bold">{{ number_format($untaxed_amount, 2) }} {{ $currency }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                <span class="text-muted">Taxes:</span>
                                <span class="fw-bold">{{ number_format($tax_amount, 2) }} {{ $currency }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="h5 fw-bold text-primary">Total Amount:</span>
                                <span class="h5 fw-bold text-primary">{{ number_format($total_amount, 2) }} {{ $currency }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- List View -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button wire:click="createNew" class="btn btn-validate shadow-sm"><i class="mdi mdi-plus me-1"></i> NEW BILL</button>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover odoo-table mb-0">
                        <thead>
                            <tr>
                                <th>Bill Number</th>
                                <th>Vendor Invoice No</th>
                                <th>Vendor</th>
                                <th>Source PO</th>
                                <th>Bill Date</th>
                                <th>Due Date</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bills as $bill)
                                <tr wire:click="edit('{{ $bill->id }}')" style="cursor:pointer">
                                    <td class="fw-bold text-primary">{{ $bill->bill_number }}</td>
                                    <td>{{ $bill->vendor_bill_number ?: '-' }}</td>
                                    <td class="fw-bold">{{ $bill->supplier->supplier_name }}</td>
                                    <td>{{ $bill->purchaseOrder ? $bill->purchaseOrder->po_number : 'Direct' }}</td>
                                    <td>{{ $bill->bill_date->format('d M Y') }}</td>
                                    <td>{{ $bill->due_date ? $bill->due_date->format('d M Y') : '-' }}</td>
                                    <td class="text-end fw-bold text-dark">{{ number_format($bill->total_amount, 2) }} {{ $bill->currency }}</td>
                                    <td class="text-center">
                                        <span class="status-badge bg-{{ $bill->status }}">{{ $bill->status }}</span>
                                    </td>
                                    <td class="text-end text-muted small">
                                        <i class="mdi mdi-chevron-right fs-5"></i>
                                    </td>
                                </tr>
                            @endforeach
                            @if(count($bills) === 0)
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">No vendor bills found. Click "NEW BILL" to get started.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
