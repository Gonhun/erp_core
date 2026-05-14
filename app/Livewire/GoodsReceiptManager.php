<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\StockMove;
use App\Models\ProductStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GoodsReceiptManager extends Component
{
    public $receipts;
    public $editingReceiptId = null;
    public $iteration = 0;

    // Form State
    public $gr_number;
    public $po_number;
    public $supplier_name;
    public $warehouse_name;
    public $received_date;
    public $pib_no;
    public $notes;
    public $status;
    public $items = [];

    public function render()
    {
        $this->receipts = GoodsReceipt::with(['supplier', 'warehouse', 'purchaseOrder'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.goods-receipt-manager')->layout('layouts.app');
    }

    public function edit($id)
    {
        $receipt = GoodsReceipt::with(['items.product', 'supplier', 'warehouse', 'purchaseOrder'])->findOrFail($id);
        $this->editingReceiptId = $id;
        $this->gr_number = $receipt->gr_number;
        $this->po_number = $receipt->purchaseOrder->po_number;
        $this->supplier_name = $receipt->supplier->supplier_name;
        $this->warehouse_name = $receipt->warehouse->warehouse_name;
        $this->received_date = $receipt->received_date ? $receipt->received_date->format('Y-m-d') : date('Y-m-d');
        $this->pib_no = $receipt->pib_no;
        $this->notes = $receipt->notes;
        $this->status = $receipt->status;

        $this->items = $receipt->items->map(function ($item) {
            return [
                'id' => $item->id,
                'product_name' => $item->product->product_name,
                'qty_ordered' => $item->qty_ordered,
                'qty_received' => $item->qty_received,
            ];
        })->toArray();

        $this->iteration++;
    }

    public function cancel()
    {
        $this->editingReceiptId = null;
        $this->iteration++;
    }

    public function validateReceipt()
    {
        $receipt = GoodsReceipt::findOrFail($this->editingReceiptId);

        if ($receipt->status == 'done') {
            session()->flash('error', 'This receipt is already processed.');
            return;
        }

        $this->validate([
            'received_date' => 'required|date',
            'items.*.qty_received' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($receipt) {
            $hasBackorder = false;
            $backorderItems = [];

            foreach ($this->items as $index => $itemData) {
                $item = GoodsReceiptItem::findOrFail($itemData['id']);
                $qtyReceived = (float) $itemData['qty_received'];

                // 1. Update Receipt Item
                $item->update(['qty_received' => $qtyReceived]);

                if ($qtyReceived > 0) {
                    // 2. Create Stock Move
                    StockMove::create([
                        'reference' => $receipt->gr_number,
                        'product_id' => $item->product_id,
                        'warehouse_id' => $receipt->warehouse_id,
                        'qty' => $qtyReceived,
                        'type' => 'receipt',
                    ]);

                    // 3. Update Product Stock (Real-time balance)
                    $stock = ProductStock::firstOrCreate(
                        ['product_id' => $item->product_id, 'warehouse_id' => $receipt->warehouse_id],
                        ['qty_on_hand' => 0]
                    );
                    $stock->increment('qty_on_hand', $qtyReceived);
                }

                // 4. Check for Backorder
                if ($qtyReceived < $item->qty_ordered) {
                    $hasBackorder = true;
                    $backorderItems[] = [
                        'purchase_order_item_id' => $item->purchase_order_item_id,
                        'product_id' => $item->product_id,
                        'qty_remaining' => $item->qty_ordered - $qtyReceived,
                    ];
                }
            }

            // 5. Mark as Done
            $receipt->update([
                'status' => 'done',
                'received_date' => $this->received_date,
                'pib_no' => $this->pib_no,
                'notes' => $this->notes,
                'invoice_control' => 'ready_to_invoice'
            ]);

            // 6. Create Backorder if needed
            if ($hasBackorder) {
                $this->createBackorder($receipt, $backorderItems);
            }
        });

        $this->editingReceiptId = null;
        $this->iteration++;
        session()->flash('message', 'Inventory received and stock updated successfully.');
    }

    protected function createBackorder($originalReceipt, $items)
    {
        $newGrNumber = $originalReceipt->gr_number . '-BACK';
        
        // Ensure uniqueness for backorder number
        $count = 1;
        while (GoodsReceipt::where('gr_number', $newGrNumber)->exists()) {
            $newGrNumber = $originalReceipt->gr_number . '-BACK-' . $count;
            $count++;
        }

        $backorder = GoodsReceipt::create([
            'gr_number' => $newGrNumber,
            'purchase_order_id' => $originalReceipt->purchase_order_id,
            'supplier_id' => $originalReceipt->supplier_id,
            'warehouse_id' => $originalReceipt->warehouse_id,
            'currency' => $originalReceipt->currency,
            'status' => 'waiting',
            'notes' => 'Backorder from ' . $originalReceipt->gr_number,
        ]);

        foreach ($items as $item) {
            GoodsReceiptItem::create([
                'goods_receipt_id' => $backorder->id,
                'purchase_order_item_id' => $item['purchase_order_item_id'],
                'product_id' => $item['product_id'],
                'qty_ordered' => $item['qty_remaining'],
                'qty_received' => 0,
            ]);
        }
    }
}
