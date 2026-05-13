<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\UomCategory;
use App\Models\Tax;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseOrderManager extends Component
{
    public $iteration = 0;
    public $isCreating = false;
    public $editingId = null;
    
    // Header Fields
    public $po_number = '';
    public $supplier_id = '';
    public $order_date = '';
    public $order_deadline = '';
    public $expected_arrival = '';
    public $currency = 'IDR';
    public $warehouse_id = '';
    public $status = 'draft';
    public $notes = '';

    // Totals
    public $untaxed_amount = 0;
    public $tax_amount = 0;
    public $total_amount = 0;

    // Items
    public $items = []; // Array of ['product_id', 'description', 'quantity', 'uom_category_id', 'unit_price', 'discount', 'tax_id', 'tax_amount', 'subtotal']

    public function mount()
    {
        $this->order_date = date('Y-m-d');
        $firstWarehouse = Warehouse::first();
        if ($firstWarehouse) {
            $this->warehouse_id = $firstWarehouse->id;
        }
    }

    public function render()
    {
        $orders = PurchaseOrder::with(['supplier', 'warehouse'])->orderBy('created_at', 'desc')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('supplier_name')->get();
        $products = Product::where('is_active', true)->orderBy('product_name')->get();
        $warehouses = Warehouse::orderBy('warehouse_name')->get();
        $allTaxes = Tax::where('is_active', true)->orderBy('tax_name')->get();

        return view('livewire.purchase-order-manager', [
            'orders' => $orders,
            'suppliers' => $suppliers,
            'products' => $products,
            'warehouses' => $warehouses,
            'allTaxes' => $allTaxes,
        ])->layout('layouts.app');
    }

    public function createNew()
    {
        $this->isCreating = true;
        $this->editingId = null;
        $this->resetFields();
        $this->addItemRow();
        $this->iteration++;
    }

    private function resetFields()
    {
        $this->po_number = '';
        $this->supplier_id = '';
        $this->order_date = date('Y-m-d');
        $this->order_deadline = '';
        $this->expected_arrival = '';
        $this->currency = 'IDR';
        $firstWarehouse = Warehouse::first();
        $this->warehouse_id = $firstWarehouse ? $firstWarehouse->id : '';
        $this->status = 'draft';
        $this->notes = '';
        $this->items = [];
        $this->calculateTotals();
    }

    public function addItemRow()
    {
        $defaultTaxId = null;
        if (!empty($this->supplier_id)) {
            $supplier = Supplier::find($this->supplier_id);
            if ($supplier && $supplier->is_ppn) {
                $ppnTax = Tax::where('is_ppn', true)->where('is_active', true)->first();
                $defaultTaxId = $ppnTax ? $ppnTax->id : null;
            }
        }

        $this->items[] = [
            'product_id' => '',
            'description' => '',
            'quantity' => 1,
            'uom_category_id' => '',
            'unit_price' => 0,
            'discount' => 0,
            'tax_id' => $defaultTaxId,
            'tax_amount' => 0,
            'subtotal' => 0
        ];
    }

    public function removeItemRow($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
    }

    public function updatedItems($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) < 2) return;
        
        $index = $parts[0];
        $field = $parts[1];

        if ($field === 'product_id' && !empty($value)) {
            $product = Product::with('uomCategory')->find($value);
            if ($product) {
                $this->items[$index]['description'] = $product->product_name;
                $this->items[$index]['uom_category_id'] = $product->uom_category_id;
            }
        }

        // Recalculate row
        $this->recalculateRow($index);
        $this->calculateTotals();
    }

    private function recalculateRow($index)
    {
        $item = &$this->items[$index];
        $qty = floatval($item['quantity'] ?: 0);
        $price = floatval($item['unit_price'] ?: 0);
        $discPercent = floatval($item['discount'] ?: 0);
        
        $gross = $qty * $price;
        $discAmount = $gross * ($discPercent / 100);
        $item['subtotal'] = $gross - $discAmount;

        // Tax calculation per line
        $item['tax_amount'] = 0;
        if (!empty($item['tax_id'])) {
            $tax = Tax::find($item['tax_id']);
            if ($tax) {
                // Assuming tax is applied to subtotal (after discount)
                $item['tax_amount'] = $item['subtotal'] * ($tax->tax_amount / 100);
            }
        }
    }

    public function updatedSupplierId($value)
    {
        // When vendor changes, we might want to update existing lines' taxes if they are empty
        if (!empty($value)) {
            $supplier = Supplier::find($value);
            if ($supplier && $supplier->is_ppn) {
                $ppnTax = Tax::where('is_ppn', true)->where('is_active', true)->first();
                if ($ppnTax) {
                    foreach ($this->items as $idx => $item) {
                        if (empty($item['tax_id'])) {
                            $this->items[$idx]['tax_id'] = $ppnTax->id;
                            $this->recalculateRow($idx);
                        }
                    }
                }
            }
        }
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->untaxed_amount = array_sum(array_column($this->items, 'subtotal'));
        $this->tax_amount = array_sum(array_column($this->items, 'tax_amount'));
        $this->total_amount = $this->untaxed_amount + $this->tax_amount;
    }

    public function save()
    {
        $this->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::transaction(function () {
            if (empty($this->po_number)) {
                $year = date('Y', strtotime($this->order_date));
                $month = date('m', strtotime($this->order_date));
                $prefix = 'PO-' . $year . $month . '-';
                
                $lastPO = PurchaseOrder::where('po_number', 'like', $prefix . '%')->orderBy('po_number', 'desc')->first();
                $lastNum = $lastPO ? intval(substr($lastPO->po_number, -4)) : 0;
                $this->po_number = $prefix . str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
            }

            $data = [
                'po_number' => $this->po_number,
                'supplier_id' => $this->supplier_id,
                'order_date' => $this->order_date,
                'order_deadline' => $this->order_deadline ?: null,
                'expected_arrival' => $this->expected_arrival ?: null,
                'currency' => $this->currency,
                'warehouse_id' => $this->warehouse_id,
                'status' => $this->status,
                'untaxed_amount' => $this->untaxed_amount,
                'tax_amount' => $this->tax_amount,
                'total_amount' => $this->total_amount,
                'notes' => $this->notes,
            ];

            if ($this->editingId) {
                $order = PurchaseOrder::findOrFail($this->editingId);
                $order->update($data);
            } else {
                $order = PurchaseOrder::create($data);
            }

            PurchaseOrderItem::where('purchase_order_id', $order->id)->delete();
            foreach ($this->items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'uom_category_id' => $item['uom_category_id'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?: 0,
                    'tax_id' => $item['tax_id'] ?: null,
                    'tax_amount' => $item['tax_amount'] ?: 0,
                    'subtotal' => $item['subtotal'],
                ]);
            }
        });

        $this->isCreating = false;
        $this->editingId = null;
        $this->iteration++;
        session()->flash('message', 'Purchase Quotation saved.');
    }

    public function edit($id)
    {
        $order = PurchaseOrder::with('items')->findOrFail($id);
        $this->editingId = $id;
        $this->isCreating = false;

        $this->po_number = $order->po_number;
        $this->supplier_id = $order->supplier_id;
        $this->order_date = $order->order_date->format('Y-m-d');
        $this->order_deadline = $order->order_deadline ? $order->order_deadline->format('Y-m-d') : '';
        $this->expected_arrival = $order->expected_arrival ? $order->expected_arrival->format('Y-m-d') : '';
        $this->currency = $order->currency;
        $this->warehouse_id = $order->warehouse_id;
        $this->status = $order->status;
        $this->notes = $order->notes;
        $this->untaxed_amount = $order->untaxed_amount;
        $this->tax_amount = $order->tax_amount;
        $this->total_amount = $order->total_amount;

        $this->items = $order->items->map(fn($item) => [
            'product_id' => $item->product_id,
            'description' => $item->description,
            'quantity' => $item->quantity,
            'uom_category_id' => $item->uom_category_id,
            'unit_price' => $item->unit_price,
            'discount' => $item->discount,
            'tax_id' => $item->tax_id,
            'tax_amount' => $item->tax_amount,
            'subtotal' => $item->subtotal,
        ])->toArray();

        $this->iteration++;
    }

    public function confirmRfq()
    {
        if (!$this->editingId) return;
        $order = PurchaseOrder::findOrFail($this->editingId);
        $order->update(['status' => 'sent']);
        $this->status = 'sent';
        $this->iteration++;
    }

    public function confirmOrder()
    {
        if (!$this->editingId) return;
        $order = PurchaseOrder::findOrFail($this->editingId);
        $order->update(['status' => 'purchase']);
        $this->status = 'purchase';
        $this->iteration++;
    }

    public function cancelOrder()
    {
        if (!$this->editingId) return;
        $order = PurchaseOrder::findOrFail($this->editingId);
        $order->update(['status' => 'cancel']);
        $this->status = 'cancel';
        $this->iteration++;
    }

    public function exportPdf($id)
    {
        $order = PurchaseOrder::with(['supplier', 'warehouse', 'items.product'])->findOrFail($id);
        
        $pdf = Pdf::loadView('purchasing.pdf', ['order' => $order]);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $order->po_number . '.pdf');
    }

    public function cancel()
    {
        $this->isCreating = false;
        $this->editingId = null;
        $this->resetFields();
        $this->iteration++;
    }
}
