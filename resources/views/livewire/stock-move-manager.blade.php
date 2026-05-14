<div>
    <div class="container-fluid py-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Inventory Ledger (Stock Moves)</h2>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date & Time</th>
                            <th>Reference</th>
                            <th>Product</th>
                            <th>Warehouse</th>
                            <th class="text-end">Quantity</th>
                            <th class="text-center">Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($moves as $move)
                            <tr>
                                <td class="small text-muted">{{ $move->created_at->format('d M Y H:i:s') }}</td>
                                <td class="fw-bold">{{ $move->reference }}</td>
                                <td>
                                    <div class="fw-bold">{{ $move->product->product_name }}</div>
                                    <div class="small text-muted">{{ $move->product->product_code }}</div>
                                </td>
                                <td>{{ $move->warehouse->warehouse_name }}</td>
                                <td class="text-end fw-bold {{ $move->qty > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $move->qty > 0 ? '+' : '' }}{{ number_format($move->qty, 2) }}
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill {{ $move->type == 'receipt' ? 'bg-success' : ($move->type == 'delivery' ? 'bg-primary' : 'bg-info') }}" style="font-size: 0.7rem;">
                                        {{ strtoupper($move->type) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        @if(count($moves) == 0)
                            <tr><td colspan="6" class="text-center py-5 text-muted">No stock movements recorded yet.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
