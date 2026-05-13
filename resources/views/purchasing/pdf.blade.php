<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $order->po_number }}</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; line-height: 1.4; margin: 0; padding: 0; }
        .header { margin-bottom: 40px; padding-bottom: 20px; border-bottom: 2px solid #008784; }
        .company-info { float: left; width: 50%; }
        .order-info { float: right; width: 45%; text-align: right; }
        .clearfix { clear: both; }
        .section-title { font-weight: bold; font-size: 12px; margin-bottom: 8px; color: #008784; text-transform: uppercase; border-bottom: 1px solid #eee; padding-bottom: 3px; }
        .address-box { margin-bottom: 20px; min-height: 80px; }
        .address-box div { margin-bottom: 2px; }
        
        table.items-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table.items-table th { background-color: #f9f9f9; border-bottom: 2px solid #008784; padding: 12px 8px; text-align: left; font-size: 9px; text-transform: uppercase; color: #444; }
        table.items-table td { padding: 10px 8px; border-bottom: 1px solid #eee; vertical-align: top; }
        
        .text-end { text-align: right; }
        
        /* Totals section as a table for stability */
        .totals-container { margin-top: 30px; width: 100%; }
        .totals-table { float: right; width: 280px; }
        .totals-table td { padding: 6px 0; border: none; }
        .totals-table td.label { text-align: left; color: #666; width: 150px; }
        .totals-table td.value { text-align: right; font-weight: bold; }
        .grand-total-row td { border-top: 2px solid #008784; padding-top: 12px; margin-top: 10px; }
        .grand-total-label { font-size: 14px; font-weight: bold; color: #008784; }
        .grand-total-value { font-size: 16px; font-weight: bold; color: #008784; }

        .notes-section { margin-top: 40px; padding: 15px; background-color: #fcfcfc; border: 1px solid #f0f0f0; border-radius: 4px; }
        .footer { position: fixed; bottom: 0; width: 100%; font-size: 9px; color: #aaa; text-align: center; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <h1 style="margin: 0; color: #008784; font-size: 24px;">{{ config('app.name', 'ERP CORE') }}</h1>
            <div style="margin-top: 5px;">
                <div>Jl. Industrial Estate Block C-10</div>
                <div>Bekasi, 17530</div>
                <div>Phone: +62 21 8900 1234 | Email: info@erpcore.id</div>
            </div>
        </div>
        <div class="order-info">
            <h2 style="margin: 0; font-size: 20px; color: #444;">{{ $order->status == 'purchase' ? 'PURCHASE ORDER' : 'QUOTATION' }}</h2>
            <div style="font-size: 18px; font-weight: bold; color: #008784; margin: 8px 0;">{{ $order->po_number }}</div>
            <div style="margin-top: 10px;">
                <table style="width: 100%; border: none;">
                    <tr><td style="text-align: right; color: #666; padding: 2px;">Order Date:</td><td style="text-align: right; font-weight: bold; padding: 2px;">{{ $order->order_date->format('d M Y') }}</td></tr>
                    @if($order->order_deadline)
                        <tr><td style="text-align: right; color: #666; padding: 2px;">Deadline:</td><td style="text-align: right; font-weight: bold; padding: 2px;">{{ $order->order_deadline->format('d M Y') }}</td></tr>
                    @endif
                    <tr><td style="text-align: right; color: #666; padding: 2px;">Currency:</td><td style="text-align: right; font-weight: bold; padding: 2px;">{{ $order->currency }}</td></tr>
                </table>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

    <div style="width: 100%;">
        <div style="float: left; width: 48%;">
            <div class="section-title">Vendor / Supplier</div>
            <div class="address-box">
                <div style="font-weight: bold; font-size: 13px; color: #111;">{{ $order->supplier->supplier_name }}</div>
                <div style="margin-top: 5px; color: #555;">
                    <div>{{ $order->supplier->address ?: 'No address specified' }}</div>
                    <div>Email: {{ $order->supplier->email ?: '-' }}</div>
                    <div>Phone: {{ $order->supplier->phone ?: '-' }}</div>
                    @if($order->supplier->npwp_no)
                        <div style="margin-top: 3px;">Tax ID (NPWP): {{ $order->supplier->npwp_no }}</div>
                    @endif
                </div>
            </div>
        </div>
        <div style="float: right; width: 48%;">
            <div class="section-title">Shipping To</div>
            <div class="address-box">
                <div style="font-weight: bold; font-size: 13px;">{{ $order->warehouse ? $order->warehouse->warehouse_name : 'Primary Warehouse' }}</div>
                <div style="margin-top: 5px; color: #555;">
                    <div>{{ $order->warehouse ? $order->warehouse->address : 'Warehouse address not specified' }}</div>
                    @if($order->expected_arrival)
                        <div style="margin-top: 5px;">Expected Arrival: <span style="font-weight: bold; color: #333;">{{ $order->expected_arrival->format('d M Y') }}</span></div>
                    @endif
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 45%;">Product Description</th>
                <th class="text-end" style="width: 10%;">Qty</th>
                <th class="text-end" style="width: 15%;">Unit Price</th>
                <th class="text-end" style="width: 10%;">Disc %</th>
                <th class="text-end" style="width: 20%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        <div style="font-weight: bold; font-size: 11px;">{{ $item->product->product_name }}</div>
                        <div style="font-size: 10px; color: #777; margin-top: 3px;">{{ $item->description }}</div>
                    </td>
                    <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                    <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end">{{ number_format($item->discount, 2) }}%</td>
                    <td class="text-end" style="font-weight: bold;">{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-container">
        <table class="totals-table">
            <tr>
                <td class="label">Untaxed Amount:</td>
                <td class="value">{{ number_format($order->untaxed_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Taxes:</td>
                <td class="value">{{ number_format($order->tax_amount, 2) }}</td>
            </tr>
            <tr class="grand-total-row">
                <td class="grand-total-label">TOTAL ({{ $order->currency }}):</td>
                <td class="grand-total-value">{{ number_format($order->total_amount, 2) }}</td>
            </tr>
        </table>
        <div class="clearfix"></div>
    </div>

    @if($order->notes)
        <div class="notes-section">
            <div class="section-title">Terms & Conditions</div>
            <div style="color: #555; font-size: 10px;">{{ $order->notes }}</div>
        </div>
    @endif

    <div class="footer">
        This is a computer-generated document. No signature is required. <br>
        Printed on {{ now()->format('d M Y H:i:s') }}
    </div>
</body>
</html>
