<div>
    <style>
        .odoo-table th { color: #495057; font-weight: 600; font-size: 0.85rem; border-bottom: 2px solid #dee2e6; padding: 12px 16px; }
        .odoo-table td { vertical-align: middle; padding: 8px 16px; border-bottom: 1px solid #eee; font-size: 0.9rem; }
        .btn-validate { background-color: #008784; color: white; font-weight: bold; }
        .btn-validate:hover { background-color: #006e6b; color: white; }
        .status-badge { font-size: 0.75rem; padding: 4px 10px; border-radius: 20px; font-weight: bold; text-transform: uppercase; }
        .bg-draft { background-color: #e9ecef; color: #495057; }
        .bg-posted { background-color: #cce5ff; color: #004085; }
        .bg-cancel { background-color: #f8d7da; color: #721c24; }
        .form-label { font-size: 0.8rem; margin-bottom: 4px; color: #666; font-weight: 600; }
        .form-control, .form-select { border-radius: 2px; font-size: 0.9rem; border: 1px solid #ccc; }
        .form-control:focus, .form-select:focus { border-color: #008784; box-shadow: 0 0 0 0.2rem rgba(0, 135, 132, 0.15); }
    </style>

    <div class="container-fluid py-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Vendor Payments</h2>
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

        @if($isCreating || $editingPaymentId)
            <!-- Form View -->
            <div class="card border-0 shadow-sm" wire:key="payment-form-{{ $iteration }}">
                <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2">
                        @if($status === 'draft')
                            <button wire:click="save" wire:confirm="Save draft payment?" class="btn btn-sm btn-validate px-3 shadow-sm">SAVE</button>
                            @if($editingPaymentId)
                                <button wire:click="postPayment" wire:confirm="Are you sure you want to POST this payment? This will record bank payout and clear allocated invoices!" class="btn btn-sm btn-primary px-3 shadow-sm">POST PAYMENT</button>
                            @endif
                        @endif

                        @if($status === 'posted')
                            <button wire:click="cancelPayment" wire:confirm="Are you sure you want to cancel this payment? Allocated bills will be restored to unpaid!" class="btn btn-sm btn-danger px-3 shadow-sm">CANCEL PAYMENT</button>
                        @endif

                        <button wire:click="discard" class="btn btn-sm btn-light border px-3">DISCARD</button>
                    </div>
                    <div class="text-muted small">
                        Number: <strong>{{ $payment_number }}</strong>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h1 class="h3 fw-bold text-muted">{{ $payment_number }}</h1>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="status-badge bg-{{ $status }}">{{ $status }}</span>
                        </div>
                    </div>

                    <div class="row g-3 mb-5">
                        <div class="col-md-4">
                            <label class="form-label">Vendor / Supplier</label>
                            @if($status !== 'draft')
                                <div class="fs-6 fw-bold">{{ $suppliersList->firstWhere('id', $supplier_id)->supplier_name ?? '-' }}</div>
                            @else
                                <select class="form-select" wire:model.live="supplier_id">
                                    <option value="">Select Vendor...</option>
                                    @foreach($suppliersList as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Payment Date</label>
                            @if($status !== 'draft')
                                <div class="fs-6">{{ $payment_date }}</div>
                            @else
                                <input type="date" class="form-control" wire:model="payment_date">
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Payment Method</label>
                            @if($status !== 'draft')
                                <div class="fs-6">{{ $payment_method }}</div>
                            @else
                                <select class="form-select" wire:model="payment_method">
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Payment Amount</label>
                            @if($status !== 'draft')
                                <div class="fs-5 fw-bold text-primary">{{ number_format($amount, 2) }} {{ $currency }}</div>
                            @else
                                <input type="number" step="0.01" class="form-control fs-5 fw-bold text-primary" wire:model="amount" placeholder="0.00">
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Journal Account (Outflow Account)</label>
                            @if($status !== 'draft')
                                <div class="fs-6">{{ $accountsList->firstWhere('id', $journal_account_id)->account_code ?? '' }} - {{ $accountsList->firstWhere('id', $journal_account_id)->account_name ?? '' }}</div>
                            @else
                                <select class="form-select" wire:model="journal_account_id">
                                    @foreach($accountsList as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Payment Reference</label>
                            @if($status !== 'draft')
                                <div class="fs-6">{{ $reference ?: '-' }}</div>
                            @else
                                <input type="text" class="form-control" wire:model="reference" placeholder="Receipt or bank reference code...">
                            @endif
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Notes</label>
                            @if($status !== 'draft')
                                <div class="fs-6">{{ $notes ?: '-' }}</div>
                            @else
                                <textarea class="form-control" wire:model="notes" rows="1" placeholder="Payment description..."></textarea>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Open Vendor Bills Allocation</h5>
                        @if($status === 'draft' && $amount > 0)
                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="autoAllocate">Auto Allocate</button>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Bill Number</th>
                                    <th class="text-end">Total Bill Amount</th>
                                    <th class="text-end">Outstanding Balance</th>
                                    <th class="text-end" style="width: 250px;">Allocated Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allocations as $index => $alloc)
                                    <tr wire:key="alloc-{{ $index }}">
                                        <td class="fw-bold">{{ $alloc['bill_number'] }}</td>
                                        <td class="text-end">{{ number_format($alloc['total_amount'], 2) }} {{ $currency }}</td>
                                        <td class="text-end text-danger fw-bold">{{ number_format($alloc['amount_due'], 2) }} {{ $currency }}</td>
                                        <td class="text-end">
                                            @if($status !== 'draft')
                                                <span class="fw-bold text-success">{{ number_format($alloc['amount_allocated'], 2) }} {{ $currency }}</span>
                                            @else
                                                <div class="input-group input-group-sm justify-content-end">
                                                    <input type="number" step="0.01" class="form-control text-end border-0 bg-light" style="max-width: 150px;" wire:model.live="allocations.{{ $index }}.amount_allocated">
                                                    <button class="btn btn-outline-secondary border-0" type="button" onclick="@this.set('allocations.{{ $index }}.amount_allocated', {{ $alloc['amount_due'] }})" title="Pay Fully">
                                                        <i class="mdi mdi-arrow-left-bold"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                @if(count($allocations) === 0)
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">No outstanding bills available for this supplier.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <!-- List View -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button wire:click="createNew" class="btn btn-validate shadow-sm"><i class="mdi mdi-plus me-1"></i> REGISTER PAYMENT</button>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover odoo-table mb-0">
                        <thead>
                            <tr>
                                <th>Payment Number</th>
                                <th>Vendor</th>
                                <th>Payment Date</th>
                                <th>Method</th>
                                <th>Bank/Cash Account</th>
                                <th>Reference</th>
                                <th class="text-end">Paid Amount</th>
                                <th class="text-center">Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr wire:click="edit('{{ $payment->id }}')" style="cursor:pointer">
                                    <td class="fw-bold text-primary">{{ $payment->payment_number }}</td>
                                    <td class="fw-bold">{{ $payment->supplier ? $payment->supplier->supplier_name : '-' }}</td>
                                    <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                    <td>{{ $payment->payment_method }}</td>
                                    <td><span class="small">{{ $payment->journalAccount->account_code }} - {{ $payment->journalAccount->account_name }}</span></td>
                                    <td>{{ $payment->reference ?: '-' }}</td>
                                    <td class="text-end fw-bold text-success">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
                                    <td class="text-center">
                                        <span class="status-badge bg-{{ $payment->status }}">{{ $payment->status }}</span>
                                    </td>
                                    <td class="text-end text-muted small">
                                        <i class="mdi mdi-chevron-right fs-5"></i>
                                    </td>
                                </tr>
                            @endforeach
                            @if(count($payments) === 0)
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">No payments registered. Click "REGISTER PAYMENT" to get started.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
