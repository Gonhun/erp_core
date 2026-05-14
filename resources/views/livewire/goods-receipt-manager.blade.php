<div>
    <style>
        .odoo-table th { color: #495057; font-weight: 600; font-size: 0.85rem; border-bottom: 2px solid #dee2e6; padding: 12px 16px; }
        .odoo-table td { vertical-align: middle; padding: 10px 16px; border-bottom: 1px solid #eee; font-size: 0.9rem; }
        .btn-validate { background-color: #008784; color: white; font-weight: bold; }
        .btn-validate:hover { background-color: #006e6b; color: white; }
        .status-badge { font-size: 0.75rem; padding: 4px 10px; border-radius: 20px; font-weight: bold; text-transform: uppercase; }
        .bg-draft { background-color: #e9ecef; color: #495057; }
        .bg-waiting { background-color: #fff3cd; color: #856404; }
        .bg-ready { background-color: #cce5ff; color: #004085; }
        .bg-done { background-color: #d4edda; color: #155724; }
        .bg-cancel { background-color: #f8d7da; color: #721c24; }
    </style>

    <div class="container-fluid py-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Inventory Receipts</h2>
        </div>

        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($editingReceiptId)
            <div class="card border-0 shadow-sm" wire:key="gr-form-{{ $iteration }}">
                <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2">
                        @if($status != 'done' && $status != 'cancel')
                            <button wire:click="validateReceipt" wire:confirm="Are you sure you want to validate this receipt and update stock?" class="btn btn-validate shadow-sm">VALIDATE</button>
                        @endif
                        <button wire:click="cancel" class="btn btn-light border">BACK</button>
                    </div>
                    <div class="text-muted small">
                        Source: <strong>{{ $po_number }}</strong>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h1 class="h3 fw-bold text-muted">{{ $gr_number }}</h1>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="status-badge bg-{{ $status }}">{{ $status }}</span>
                        </div>
                    </div>

                    <div class="row g-4 mb-5">
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Vendor</label>
                            <div class="fs-5 fw-bold">{{ $supplier_name }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Destination Warehouse</label>
                            <div class="fs-6">{{ $warehouse_name }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Received Date</label>
                            @if($status == 'done')
                                <div class="fs-6">{{ $received_date }}</div>
                            @else
                                <input type="date" class="form-control" wire:model="received_date">
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">PIB Number</label>
                            @if($status == 'done')
                                <div class="fs-6">{{ $pib_no ?: '-' }}</div>
                            @else
                                <input type="text" class="form-control" wire:model="pib_no" placeholder="Optional PIB Number...">
                            @endif
                        </div>
                        <div class="col-md-8">
                            <label class="form-label text-muted small fw-bold">Internal Notes</label>
                            @if($status == 'done')
                                <div class="fs-6">{{ $notes ?: '-' }}</div>
                            @else
                                <textarea class="form-control" wire:model="notes" rows="1" placeholder="Add warehouse notes..."></textarea>
                            @endif
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3 border-bottom pb-2">Operations</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th class="text-end" style="width: 150px;">Ordered</th>
                                    <th class="text-end" style="width: 200px;">Done / Received</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $index => $item)
                                    <tr wire:key="item-{{ $index }}">
                                        <td class="fw-bold">{{ $item['product_name'] }}</td>
                                        <td class="text-end">{{ number_format($item['qty_ordered'], 2) }}</td>
                                        <td class="text-end">
                                            @if($status == 'done')
                                                <span class="fw-bold text-success">{{ number_format($item['qty_received'], 2) }}</span>
                                            @else
                                                <div class="input-group input-group-sm justify-content-end">
                                                    <input type="number" step="0.01" class="form-control text-end border-0 bg-light" style="max-width: 120px;" wire:model.live="items.{{ $index }}.qty_received">
                                                    <button class="btn btn-outline-secondary border-0" type="button" onclick="@this.set('items.{{ $index }}.qty_received', {{ $item['qty_ordered'] }})" title="Set Full">
                                                        <i class="mdi mdi-arrow-left-bold"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover odoo-table mb-0">
                        <thead>
                            <tr>
                                <th>GR Number</th>
                                <th>Source PO</th>
                                <th>Vendor</th>
                                <th>Warehouse</th>
                                <th>Date</th>
                                <th class="text-center">Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receipts as $r)
                                <tr wire:click="edit('{{ $r->id }}')" style="cursor:pointer">
                                    <td class="fw-bold text-primary">{{ $r->gr_number }}</td>
                                    <td>{{ $r->purchaseOrder->po_number }}</td>
                                    <td>{{ $r->supplier->supplier_name }}</td>
                                    <td>{{ $r->warehouse->warehouse_name }}</td>
                                    <td>{{ $r->received_date ? $r->received_date->format('d M Y') : '---' }}</td>
                                    <td class="text-center">
                                        <span class="status-badge bg-{{ $r->status }}">{{ $r->status }}</span>
                                    </td>
                                    <td class="text-end text-muted small">
                                        <i class="mdi mdi-chevron-right fs-5"></i>
                                    </td>
                                </tr>
                            @endforeach
                            @if(count($receipts) == 0)
                                <tr><td colspan="7" class="text-center py-5 text-muted">No inventory receipts found.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
