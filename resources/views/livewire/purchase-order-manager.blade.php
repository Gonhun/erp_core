<div>
    <style>
        .odoo-status-bar { background-color: #f8f9fa; border-bottom: 1px solid #dee2e6; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; }
        .odoo-breadcrumb .step { font-weight: 600; color: #adb5bd; padding: 0 15px; position: relative; }
        .odoo-breadcrumb .step.active { color: #008784; }
        .odoo-breadcrumb .step:not(:last-child)::after { content: '>'; position: absolute; right: -5px; color: #dee2e6; }
        .form-section-title { font-size: 1.1rem; font-weight: bold; color: #333; margin-bottom: 15px; border-bottom: 2px solid #008784; display: inline-block; }
        .line-item-table th { background-color: #f8f9fa; text-transform: uppercase; font-size: 0.75rem; color: #666; }
        .total-box { background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 4px; }
        .total-row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .total-row.grand-total { border-top: 1px solid #dee2e6; padding-top: 10px; margin-top: 10px; font-weight: bold; font-size: 1.2rem; color: #008784; }
    </style>

    <div class="container-fluid py-3 bg-white">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('purchasing.home') }}" class="text-muted text-decoration-none small"><i class="mdi mdi-arrow-left"></i> Back to Dashboard</a>
                <h2 class="mt-2 fw-bold">Purchase Orders</h2>
            </div>
            @if(!$isCreating && !$editingId)
                <button wire:click="createNew" class="btn btn-new shadow-sm"><i class="mdi mdi-plus"></i> NEW QUOTATION</button>
            @endif
        </div>

        @if($isCreating || $editingId)
            <div class="card border-0 shadow-sm" style="border: 1px solid #dee2e6 !important;" wire:key="po-form-{{ $iteration }}">
                <div class="odoo-status-bar">
                    <div class="d-flex gap-2">
                        @if($status == 'draft')
                            <button wire:click="save" wire:confirm="Save this quotation?" class="btn btn-sm btn-new px-3 shadow-sm">SAVE</button>
                            <button wire:click="confirmRfq" wire:confirm="Confirm this RFQ and mark as sent?" class="btn btn-sm btn-primary px-3 shadow-sm"><i class="mdi mdi-email-outline"></i> CONFIRM RFQ</button>
                        @elseif($status == 'sent')
                            <button wire:click="confirmOrder" wire:confirm="Confirm this order and generate Inventory Receipt?" class="btn btn-sm btn-success px-3 shadow-sm"><i class="mdi mdi-check-circle-outline"></i> CONFIRM ORDER</button>
                        @endif
                        
                        @if($editingId)
                            <button wire:click="exportPdf('{{ $editingId }}')" class="btn btn-sm btn-outline-danger px-3"><i class="mdi mdi-file-pdf-box"></i> PRINT PDF</button>
                        @endif

                        <div class="vr mx-2"></div>

                        <button wire:click="cancel" class="btn btn-sm btn-light border px-3">DISCARD</button>
                        
                        @if($status != 'cancel' && $status != 'done')
                            <button wire:click="cancelOrder" wire:confirm="Cancel this order?" class="btn btn-sm btn-link text-danger text-decoration-none">CANCEL ORDER</button>
                        @endif
                    </div>
                    <div class="odoo-breadcrumb d-none d-md-flex">
                        <span class="step {{ $status == 'draft' ? 'active' : '' }}">QUOTATION</span>
                        <span class="step {{ $status == 'sent' ? 'active' : '' }}">RFQ SENT</span>
                        <span class="step {{ $status == 'purchase' ? 'active' : '' }}">PURCHASE ORDER</span>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h1 class="h3 fw-bold text-muted">{{ $po_number ?: 'New Quotation' }}</h1>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="badge {{ $status == 'draft' ? 'bg-secondary' : ($status == 'sent' ? 'bg-info' : 'bg-success') }} fs-6">
                                {{ strtoupper($status) }}
                            </span>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-7">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">Vendor</label>
                                    <select class="form-select border-0 bg-light shadow-none fs-5 fw-bold" wire:model.live="supplier_id">
                                        <option value="">Select a Vendor...</option>
                                        @foreach($suppliers as $sup)
                                            <option value="{{ $sup->id }}">{{ $sup->supplier_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">Order Date</label>
                                    <input type="date" class="form-control border-0 bg-light shadow-none" wire:model="order_date">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">Order Deadline</label>
                                    <input type="date" class="form-control border-0 bg-light shadow-none" wire:model="order_deadline">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">Deliver To (Warehouse)</label>
                                    <select class="form-select border-0 bg-light shadow-none" wire:model="warehouse_id">
                                        <option value="">Select Warehouse...</option>
                                        @foreach($warehouses as $w)
                                            <option value="{{ $w->id }}">{{ $w->warehouse_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('warehouse_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">Currency</label>
                                    <select class="form-select border-0 bg-light shadow-none" wire:model="currency">
                                        <option value="IDR">IDR - Indonesian Rupiah</option>
                                        <option value="USD">USD - US Dollar</option>
                                        <option value="SGD">SGD - Singapore Dollar</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">Expected Arrival</label>
                                    <input type="date" class="form-control border-0 bg-light shadow-none" wire:model="expected_arrival">
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">Internal Notes</label>
                                    <textarea class="form-control border-0 bg-light shadow-none" rows="3" wire:model="notes" placeholder="Terms and conditions..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5">
                        <h5 class="form-section-title">Order Lines</h5>
                        <div class="table-responsive">
                            <table class="table line-item-table">
                                <thead>
                                    <tr>
                                        <th style="width: 25%;">Product</th>
                                        <th style="width: 20%;">Description</th>
                                        <th style="width: 10%;" class="text-end">Qty</th>
                                        <th style="width: 12%;" class="text-end">Price</th>
                                        <th style="width: 8%;" class="text-end">Disc %</th>
                                        <th style="width: 15%;">Taxes</th>
                                        <th style="width: 12%;" class="text-end">Subtotal</th>
                                        <th style="width: 30px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $index => $item)
                                        <tr wire:key="item-row-{{ $index }}">
                                            <td>
                                                <select class="form-select border-0 shadow-none p-1" wire:model.live="items.{{ $index }}.product_id" style="font-size: 0.85rem;">
                                                    <option value="">Select Product...</option>
                                                    @foreach($products as $p)
                                                        <option value="{{ $p->id }}">{{ $p->product_code }} - {{ $p->product_name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" class="form-control border-0 shadow-none p-1" wire:model="items.{{ $index }}.description" style="font-size: 0.85rem;"></td>
                                            <td><input type="number" class="form-control border-0 shadow-none text-end p-1" wire:model.live="items.{{ $index }}.quantity" style="font-size: 0.85rem;"></td>
                                            <td><input type="number" step="0.01" class="form-control border-0 shadow-none text-end p-1" wire:model.live="items.{{ $index }}.unit_price" style="font-size: 0.85rem;"></td>
                                            <td><input type="number" step="0.01" class="form-control border-0 shadow-none text-end p-1" wire:model.live="items.{{ $index }}.discount" style="font-size: 0.85rem;"></td>
                                            <td>
                                                <select class="form-select border-0 shadow-none p-1" wire:model.live="items.{{ $index }}.tax_id" style="font-size: 0.85rem;">
                                                    <option value="">No Tax</option>
                                                    @foreach($allTaxes as $tax)
                                                        <option value="{{ $tax->id }}">{{ $tax->tax_name }} ({{ $tax->tax_amount }}%)</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="text-end py-2 fw-bold" style="font-size: 0.85rem;">
                                                {{ number_format($items[$index]['subtotal'], 2) }}
                                            </td>
                                            <td>
                                                <button class="btn btn-link text-danger p-0 mt-1" wire:click="removeItemRow({{ $index }})"><i class="mdi mdi-close"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <button class="btn btn-link text-decoration-none p-0 fw-bold" style="color: #008784;" wire:click="addItemRow">
                                <i class="mdi mdi-plus-circle"></i> Add a product
                            </button>
                        </div>
                    </div>

                    <div class="row mt-5 justify-content-end">
                        <div class="col-md-4">
                            <div class="total-box shadow-sm">
                                <div class="total-row">
                                    <span class="text-muted">Untaxed Amount:</span>
                                    <span class="fw-bold">{{ number_format($untaxed_amount, 2) }}</span>
                                </div>
                                <div class="total-row">
                                    <span class="text-muted">Taxes:</span>
                                    <span class="fw-bold">{{ number_format($tax_amount, 2) }}</span>
                                </div>
                                <div class="total-row grand-total">
                                    <span>Total ({{ $currency }}):</span>
                                    <span>{{ number_format($total_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table odoo-table mb-0 table-hover">
                        <thead>
                            <tr>
                                <th>Order Number</th>
                                <th>Vendor</th>
                                <th>Deliver To</th>
                                <th>Date</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $o)
                                <tr wire:key="po-list-{{ $o->id }}-{{ $iteration }}">
                                    <td wire:click="edit('{{ $o->id }}')" style="cursor:pointer" class="fw-bold text-primary">{{ $o->po_number }}</td>
                                    <td wire:click="edit('{{ $o->id }}')" style="cursor:pointer">{{ $o->supplier->supplier_name }}</td>
                                    <td wire:click="edit('{{ $o->id }}')" style="cursor:pointer">{{ $o->warehouse ? $o->warehouse->warehouse_name : '---' }}</td>
                                    <td wire:click="edit('{{ $o->id }}')" style="cursor:pointer">{{ $o->order_date->format('d M Y') }}</td>
                                    <td wire:click="edit('{{ $o->id }}')" style="cursor:pointer" class="text-end fw-bold">
                                        {{ $o->currency }} {{ number_format($o->total_amount, 2) }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $o->status == 'draft' ? 'bg-secondary' : ($o->status == 'sent' ? 'bg-info' : ($o->status == 'purchase' ? 'bg-success' : ($o->status == 'cancel' ? 'bg-danger' : 'bg-dark'))) }} rounded-pill" style="font-size: 0.7rem;">
                                            {{ strtoupper($o->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end align-items-center gap-2">
                                            <button wire:click="exportPdf('{{ $o->id }}')" class="btn btn-sm btn-outline-danger border-0 p-1" title="Download PDF Document">
                                                <i class="mdi mdi-file-pdf-box fs-4"></i>
                                            </button>
                                            <button wire:click="edit('{{ $o->id }}')" class="btn btn-sm btn-outline-primary border-0 fw-bold px-2">
                                                VIEW
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if(count($orders) == 0)
                                <tr><td colspan="7" class="text-center py-5 text-muted">No purchase orders found. Click "NEW QUOTATION" to start.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
