<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\VendorBill;
use App\Models\VendorBillItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Account;
use App\Models\Tax;
use App\Models\JournalEntry;
use App\Models\JournalItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Ramsey\Uuid\Type\Decimal;

class VendorBillManager extends Component
{
    public $bills;
    public $editingBillId = null;
    public $isCreating = false;
    public $iteration = 0;
    public $activeTab = 'lines'; // active tab state ('lines' or 'journal')

    // Header State
    public $bill_number;
    public $vendor_bill_number;
    public $purchase_order_id = '';
    public $supplier_id = '';
    public $bill_date;
    public $due_date;
    public $currency = 'IDR';
    public $notes;
    public $status = 'draft';

    // Totals
    public $untaxed_amount = 0;
    public $tax_amount = 0;
    public $total_amount = 0;

    // Details Grid State
    public $items = [];

    // Master Dropdowns
    public $suppliersList = [];
    public $poList = [];
    public $accountsList = [];
    public $taxesList = [];

    protected $rules = [
        'supplier_id' => 'required',
        'bill_date' => 'required|date',
        'due_date' => 'nullable|date|after_or_equal:bill_date',
        'currency' => 'required|string|max:10',
        'items.*.product_id' => 'required',
        'items.*.quantity' => 'required|numeric|min:0.01',
        'items.*.unit_price' => 'required|numeric|min:0',
        'items.*.discount' => 'nullable|numeric|min:0|max:100',
        'items.*.account_id' => 'required',
    ];

    public function mount()
    {
        $this->suppliersList = Supplier::where('is_active', true)->get();
        $this->accountsList = Account::where('is_active', true)->orderBy('account_code')->get();
        $this->taxesList = Tax::where('is_active', true)->get();
        $this->bill_date = date('Y-m-d');
        $this->due_date = date('Y-m-d', strtotime('+30 days'));
    }

    public function render()
    {
        $this->bills = VendorBill::with(['supplier', 'purchaseOrder'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Update list of POs based on selected Supplier
        if (!empty($this->supplier_id)) {
            $this->poList = PurchaseOrder::where('supplier_id', $this->supplier_id)
                ->whereIn('status', ['purchase', 'done'])
                ->get();
        } else {
            $this->poList = [];
        }

        return view('livewire.vendor-bill-manager')->layout('layouts.app');
    }

    public function updatedSupplierId($value)
    {
        $this->iteration++;
        $this->purchase_order_id = '';
        $this->items = [];
        $this->calculateTotals();
    }

    public function updatedBillDate($value)
    {
        if (empty($value) || $this->status !== 'draft')
            return;

        $romanMonths = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];
        $year = date('Y', strtotime($value));
        $monthNum = intval(date('n', strtotime($value)));
        $romanMonth = $romanMonths[$monthNum] ?? 'I';
        $prefix = "BILL/{$year}/{$romanMonth}/";

        $lastBill = VendorBill::where('bill_number', 'like', $prefix . '%')
            ->orderBy('bill_number', 'desc')
            ->first();

        if ($lastBill) {
            $lastNumPart = substr($lastBill->bill_number, strrpos($lastBill->bill_number, '/') + 1);
            $lastNum = intval($lastNumPart);
            $newNum = str_pad($lastNum + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newNum = '00001';
        }

        $this->bill_number = $prefix . $newNum;
        if (empty($this->vendor_bill_number) || str_starts_with($this->vendor_bill_number, 'BILL/')) {
            $this->vendor_bill_number = $this->bill_number;
        }
    }

    public function updatedPurchaseOrderId($value)
    {
        if (empty($value)) {
            $this->items = [];
            $this->calculateTotals();
            return;
        }

        $po = PurchaseOrder::with('items.product', 'items.tax')->find($value);
        if (!$po)
            return;

        $this->currency = $po->currency ?: 'IDR';
        $this->items = [];

        // Try to find a default expense/cost account
        $defaultAccount = Account::where('account_code', 'like', '5%')
            ->orWhere('account_name', 'like', '%Cost%')
            ->orWhere('account_name', 'like', '%Expense%')
            ->first();

        if (!$defaultAccount) {
            $defaultAccount = Account::first();
        }

        foreach ($po->items as $item) {
            $subtotal = ($item->qty_ordered * $item->unit_price) * (1 - ($item->discount / 100));
            $taxAmount = 0;
            if ($item->tax) {
                // simple tax calculation based on computation
                if ($item->tax->tax_computation == 2 || $item->tax->tax_computation == 5) {
                    $taxAmount = $subtotal * ($item->tax->tax_amount / 100);
                } else {
                    $taxAmount = $item->tax->tax_amount;
                }
            }

            $this->items[] = [
                'purchase_order_item_id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->product_name,
                'description' => $item->product->product_name,
                'quantity' => round((float) $item->qty_ordered, 2),
                'unit_price' => round((float) $item->unit_price, 2),
                'discount' => round((float) ($item->discount ?: 0), 2),
                'tax_id' => $item->tax_id ?: '',
                'tax_amount' => round((float) $taxAmount, 2),
                'subtotal' => round((float) $subtotal, 2),
                'account_id' => $defaultAccount ? $defaultAccount->id : '',
            ];
        }

        $this->calculateTotals();
    }

    public function addRow()
    {
        $this->iteration++;
        $defaultAccount = Account::where('account_code', 'like', '5%')
            ->orWhere('account_name', 'like', '%Expense%')
            ->first() ?: Account::first();

        $this->items[] = [
            'purchase_order_item_id' => null,
            'product_id' => '',
            'product_name' => '',
            'description' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'discount' => 0,
            'tax_id' => '',
            'tax_amount' => 0,
            'subtotal' => 0,
            'account_id' => $defaultAccount ? $defaultAccount->id : '',
        ];
    }

    public function removeRow($index)
    {
        $this->iteration++;
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
    }

    public function updatedItems($value, $key)
    {
        $this->iteration++;
        // Format of $key is e.g. "0.quantity" or "0.product_id"
        $parts = explode('.', $key);
        if (count($parts) < 2)
            return;

        $index = $parts[0];
        $field = $parts[1];

        if ($field === 'product_id') {
            $prod = \App\Models\Product::find($this->items[$index]['product_id']);
            if ($prod) {
                $this->items[$index]['product_name'] = $prod->product_name;
                $this->items[$index]['description'] = $prod->product_name;
            }
        }

        // Recalculate subtotal for this item row
        $qty = round((float) ($this->items[$index]['quantity'] ?? 0), 2);
        $price = round((float) ($this->items[$index]['unit_price'] ?? 0), 2);
        $disc = round((float) ($this->items[$index]['discount'] ?? 0), 2);
        $taxId = $this->items[$index]['tax_id'] ?? null;

        $subtotal = ($qty * $price) * (1 - ($disc / 100));
        $this->items[$index]['subtotal'] = $subtotal;

        $taxAmount = 0;
        if (!empty($taxId)) {
            $tax = Tax::find($taxId);
            if ($tax) {
                if ($tax->tax_computation == 2 || $tax->tax_computation == 5) {
                    $taxAmount = $subtotal * ($tax->tax_amount / 100);
                } else {
                    $taxAmount = $tax->tax_amount;
                }
            }
        }
        $this->items[$index]['tax_amount'] = $taxAmount;

        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->iteration++;
        $this->untaxed_amount = 0;
        $this->tax_amount = 0;

        foreach ($this->items as $item) {
            $this->untaxed_amount += floatval($item['subtotal'] ?? 0);
            $this->tax_amount += floatval($item['tax_amount'] ?? 0);
        }

        $this->total_amount = $this->untaxed_amount + $this->tax_amount;
    }

    public function createNew()
    {
        $this->iteration++;
        $this->resetForm();
        $this->isCreating = true;
        $this->editingBillId = null;
        $this->iteration++;
    }

    public function resetForm()
    {
        $this->bill_date = date('Y-m-d');
        $this->due_date = date('Y-m-d', strtotime('+30 days'));

        // Pre-generate unique sequential Vendor Bill number
        $romanMonths = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];
        $year = date('Y', strtotime($this->bill_date));
        $monthNum = intval(date('n', strtotime($this->bill_date)));
        $romanMonth = $romanMonths[$monthNum] ?? 'I';
        $prefix = "BILL/{$year}/{$romanMonth}/";

        $lastBill = VendorBill::where('bill_number', 'like', $prefix . '%')
            ->orderBy('bill_number', 'desc')
            ->first();

        if ($lastBill) {
            $lastNumPart = substr($lastBill->bill_number, strrpos($lastBill->bill_number, '/') + 1);
            $lastNum = intval($lastNumPart);
            $newNum = str_pad($lastNum + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newNum = '00001';
        }

        $this->bill_number = $prefix . $newNum;

        $this->vendor_bill_number = $this->bill_number;
        $this->purchase_order_id = '';
        $this->supplier_id = '';
        $this->currency = 'IDR';
        $this->notes = '';
        $this->status = 'draft';
        $this->items = [];
        $this->untaxed_amount = 0;
        $this->tax_amount = 0;
        $this->total_amount = 0;
    }

    public function edit($id)
    {
        $this->iteration++;
        $bill = VendorBill::with(['items.product', 'items.tax', 'items.account', 'supplier', 'purchaseOrder'])->findOrFail($id);
        $this->editingBillId = $id;
        $this->isCreating = false;

        $this->bill_number = $bill->bill_number;
        $this->vendor_bill_number = $bill->vendor_bill_number;
        $this->purchase_order_id = $bill->purchase_order_id ?: '';
        $this->supplier_id = $bill->supplier_id;
        $this->bill_date = $bill->bill_date->format('Y-m-d');
        $this->due_date = $bill->due_date ? $bill->due_date->format('Y-m-d') : '';
        $this->currency = $bill->currency;
        $this->notes = $bill->notes;
        $this->status = $bill->status;
        $this->untaxed_amount = $bill->untaxed_amount;
        $this->tax_amount = $bill->tax_amount;
        $this->total_amount = $bill->total_amount;

        $this->items = $bill->items->map(function ($item) {
            return [
                'id' => $item->id,
                'purchase_order_item_id' => $item->purchase_order_item_id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->product_name,
                'description' => $item->description,
                'quantity' => round((float) $item->quantity, 2),
                'unit_price' => round((float) $item->unit_price, 2),
                'discount' => round((float) $item->discount, 2),
                'tax_id' => $item->tax_id ?: '',
                'tax_amount' => round((float) $item->tax_amount, 2),
                'subtotal' => round((float) $item->subtotal, 2),
                'account_id' => $item->account_id,
            ];
        })->toArray();

        $this->iteration++;
    }

    public function save()
    {
        if (count($this->items) === 0) {
            session()->flash('error', 'A bill must have at least one line item.');
            return;
        }

        $this->validate();

        try {
            DB::transaction(function () {
                $isNew = empty($this->editingBillId);

                if ($isNew) {
                    // Generate a unique sequential Vendor Bill code: BILL/{Year}/{RomanMonth}/{5-digit Sequence}
                    $romanMonths = [
                        1 => 'I',
                        2 => 'II',
                        3 => 'III',
                        4 => 'IV',
                        5 => 'V',
                        6 => 'VI',
                        7 => 'VII',
                        8 => 'VIII',
                        9 => 'IX',
                        10 => 'X',
                        11 => 'XI',
                        12 => 'XII'
                    ];
                    $year = date('Y', strtotime($this->bill_date));
                    $monthNum = intval(date('n', strtotime($this->bill_date)));
                    $romanMonth = $romanMonths[$monthNum] ?? 'I';
                    $prefix = "BILL/{$year}/{$romanMonth}/";

                    $lastBill = VendorBill::where('bill_number', 'like', $prefix . '%')
                        ->orderBy('bill_number', 'desc')
                        ->first();

                    if ($lastBill) {
                        $lastNumPart = substr($lastBill->bill_number, strrpos($lastBill->bill_number, '/') + 1);
                        $lastNum = intval($lastNumPart);
                        $newNum = str_pad($lastNum + 1, 5, '0', STR_PAD_LEFT);
                    } else {
                        $newNum = '00001';
                    }

                    $this->bill_number = $prefix . $newNum;

                    $bill = VendorBill::create([
                        'bill_number' => $this->bill_number,
                        'vendor_bill_number' => $this->vendor_bill_number,
                        'purchase_order_id' => $this->purchase_order_id ?: null,
                        'supplier_id' => $this->supplier_id,
                        'bill_date' => $this->bill_date,
                        'due_date' => $this->due_date ?: null,
                        'currency' => $this->currency,
                        'untaxed_amount' => $this->untaxed_amount,
                        'tax_amount' => $this->tax_amount,
                        'total_amount' => $this->total_amount,
                        'status' => 'draft',
                        'notes' => $this->notes,
                    ]);
                } else {
                    $bill = VendorBill::findOrFail($this->editingBillId);

                    if ($bill->status !== 'draft') {
                        throw new \Exception('Only draft bills can be updated.');
                    }

                    $bill->update([
                        'vendor_bill_number' => $this->vendor_bill_number,
                        'purchase_order_id' => $this->purchase_order_id ?: null,
                        'supplier_id' => $this->supplier_id,
                        'bill_date' => $this->bill_date,
                        'due_date' => $this->due_date ?: null,
                        'currency' => $this->currency,
                        'untaxed_amount' => $this->untaxed_amount,
                        'tax_amount' => $this->tax_amount,
                        'total_amount' => $this->total_amount,
                        'notes' => $this->notes,
                    ]);

                    // Delete existing items and recreate
                    $bill->items()->delete();
                }

                foreach ($this->items as $item) {
                    VendorBillItem::create([
                        'vendor_bill_id' => $bill->id,
                        'purchase_order_item_id' => $item['purchase_order_item_id'] ?: null,
                        'product_id' => $item['product_id'],
                        'description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'discount' => $item['discount'] ?: 0,
                        'tax_id' => $item['tax_id'] ?: null,
                        'tax_amount' => $item['tax_amount'] ?: 0,
                        'subtotal' => $item['subtotal'],
                        'account_id' => $item['account_id'],
                    ]);
                }

                $this->editingBillId = $bill->id;
                $this->isCreating = false;
            });

            session()->flash('message', 'Vendor Bill saved successfully as Draft.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error while saving: ' . $e->getMessage());
        }
    }

    public function postBill()
    {
        if (empty($this->editingBillId))
            return;

        $bill = VendorBill::findOrFail($this->editingBillId);
        if ($bill->status !== 'draft') {
            session()->flash('error', 'Only draft bills can be posted.');
            return;
        }

        try {
            DB::transaction(function () use ($bill) {
                // Find default AP account from supplier accounting setup
                $supplierAcc = \App\Models\SupplierAccounting::where('supplier_id', $bill->supplier_id)->first();
                $apAccount = $supplierAcc ? $supplierAcc->payableAccount : null;

                if (!$apAccount) {
                    // fallback to any AP / Liability account
                    $apAccount = Account::where('account_code', 'like', '2%')
                        ->orWhere('account_name', 'like', '%Payable%')
                        ->orWhere('account_name', 'like', '%Liability%')
                        ->first();
                }

                if (!$apAccount) {
                    throw new \Exception('No Accounts Payable GL account configured for this vendor or globally.');
                }

                // Create Journal Entry
                $year = date('Y', strtotime($bill->bill_date));
                $month = date('m', strtotime($bill->bill_date));
                $prefix = "JE/{$year}/{$month}/";
                $lastJE = JournalEntry::where('entry_number', 'like', $prefix . '%')
                    ->orderBy('entry_number', 'desc')
                    ->first();

                if ($lastJE) {
                    $lastNum = intval(substr($lastJE->entry_number, -4));
                    $newNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $newNum = '0001';
                }
                $jeNumber = $prefix . $newNum;

                $entry = JournalEntry::create([
                    'entry_number' => $jeNumber,
                    'entry_date' => $bill->bill_date,
                    'reference' => $bill->bill_number,
                    'notes' => 'Vendor Bill validation for ' . $bill->bill_number,
                    'status' => 'posted',
                ]);

                // Credit: Accounts Payable (Total amount of bill)
                JournalItem::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $apAccount->id,
                    'partner_id' => $bill->supplier_id,
                    'partner_type' => 'supplier',
                    'debit' => 0,
                    'credit' => $bill->total_amount,
                    'name' => "Accounts Payable - Bill " . $bill->bill_number,
                ]);

                // Debits: Expense/Asset account per bill line
                foreach ($bill->items as $item) {
                    JournalItem::create([
                        'journal_entry_id' => $entry->id,
                        'account_id' => $item->account_id,
                        'partner_id' => $bill->supplier_id,
                        'partner_type' => 'supplier',
                        'debit' => $item->subtotal,
                        'credit' => 0,
                        'name' => $item->description ?: "Product expense",
                    ]);

                    // Debits for Tax if it has tax
                    if ($item->tax_amount > 0 && $item->tax_id) {
                        // Try to find the tax's definition account
                        $taxDef = \App\Models\TaxInvoiceDefinition::where('tax_id', $item->tax_id)->first();
                        $taxAccount = $taxDef ? $taxDef->account : null;

                        if (!$taxAccount) {
                            // Fallback to any tax input / VAT / asset account starting with 1
                            $taxAccount = Account::where('account_code', 'like', '1%')
                                ->where('account_name', 'like', '%Tax%')
                                ->orWhere('account_name', 'like', '%VAT%')
                                ->first() ?: Account::first();
                        }

                        JournalItem::create([
                            'journal_entry_id' => $entry->id,
                            'account_id' => $taxAccount->id,
                            'partner_id' => $bill->supplier_id,
                            'partner_type' => 'supplier',
                            'debit' => $item->tax_amount,
                            'credit' => 0,
                            'name' => "Tax Input: " . ($item->tax ? $item->tax->tax_name : 'Tax'),
                        ]);
                    }
                }

                // Update status
                $bill->update(['status' => 'posted']);
                $this->status = 'posted';
            });

            session()->flash('message', 'Vendor Bill posted successfully to Accounting Ledger.');
        } catch (\Exception $e) {
            dd($e);
            session()->flash('error', 'Failed to post Vendor Bill: ' . $e->getMessage());
        }
    }

    public function cancelBill()
    {
        if (empty($this->editingBillId))
            return;

        $bill = VendorBill::findOrFail($this->editingBillId);
        if ($bill->status === 'paid') {
            session()->flash('error', 'Cannot cancel a paid bill.');
            return;
        }

        try {
            DB::transaction(function () use ($bill) {
                // Find and delete/cancel any matching journal entry
                $je = JournalEntry::where('reference', $bill->bill_number)->first();
                if ($je) {
                    $je->update(['status' => 'cancel']);
                    // Or simply delete to keep clean
                    $je->delete();
                }

                $bill->update(['status' => 'cancel']);
                $this->status = 'cancel';
            });

            session()->flash('message', 'Vendor Bill cancelled and accounting entries reversed.');
            $this->iteration++;
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to cancel Vendor Bill: ' . $e->getMessage());
        }
    }

    public function deleteBill($id)
    {
        try {
            $bill = VendorBill::findOrFail($id);
            if ($bill->status !== 'draft') {
                session()->flash('error', 'Only draft bills can be deleted.');
                return;
            }

            $bill->delete();
            session()->flash('message', 'Draft bill deleted successfully.');
            $this->iteration++;
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete Vendor Bill: ' . $e->getMessage());
        }
    }

    public function discard()
    {
        $this->isCreating = false;
        $this->editingBillId = null;
        $this->iteration++;
    }

    public function selectTab($tab)
    {
        $this->iteration++;
        $this->activeTab = $tab;
    }

    public function getJournalItems()
    {
        if ($this->status !== 'draft') {
            // Load posted journal entries from the database
            $je = JournalEntry::where('reference', $this->bill_number)->first();
            if ($je) {
                return JournalItem::where('journal_entry_id', $je->id)
                    ->with('account')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'account_code' => $item->account->account_code,
                            'account_name' => $item->account->account_name,
                            'label' => $item->name,
                            'debit' => (float) $item->debit,
                            'credit' => (float) $item->credit,
                        ];
                    })->toArray();
            }
            return [];
        }

        // Draft state: Compute preview on-the-fly!
        $preview = [];

        // 1. Credit entry for Accounts Payable
        $supplierAcc = \App\Models\SupplierAccounting::where('supplier_id', $this->supplier_id)->first();
        $apAccount = $supplierAcc ? $supplierAcc->payableAccount : null;
        if (!$apAccount) {
            $apAccount = Account::where('account_code', 'like', '2%')
                ->orWhere('account_name', 'like', '%Payable%')
                ->first();
        }

        $preview[] = [
            'account_code' => $apAccount ? $apAccount->account_code : '200.00.001',
            'account_name' => $apAccount ? $apAccount->account_name : 'Account Payable (Draft Preview)',
            'label' => "Accounts Payable - " . ($this->bill_number ?: 'Draft'),
            'debit' => 0.0,
            'credit' => floatval($this->total_amount),
        ];

        // 2. Debit entries for product items
        foreach ($this->items as $item) {
            if (empty($item['account_id']))
                continue;
            $acc = $this->accountsList->firstWhere('id', $item['account_id']);
            $preview[] = [
                'account_code' => $acc ? $acc->account_code : '',
                'account_name' => $acc ? $acc->account_name : 'Expense Account',
                'label' => $item['description'] ?: 'Product Expense',
                'debit' => floatval($item['subtotal'] ?? 0),
                'credit' => 0.0,
            ];

            // 3. Debit entries for tax
            if (floatval($item['tax_amount'] ?? 0) > 0) {
                $taxDef = \App\Models\TaxInvoiceDefinition::where('tax_id', $item['tax_id'])->first();
                $taxAccount = $taxDef ? $taxDef->account : null;
                if (!$taxAccount) {
                    $taxAccount = Account::where('account_code', 'like', '1%')
                        ->where('account_name', 'like', '%Tax%')
                        ->orWhere('account_name', 'like', '%VAT%')
                        ->first() ?: Account::first();
                }

                $taxModel = Tax::find($item['tax_id']);
                $preview[] = [
                    'account_code' => $taxAccount ? $taxAccount->account_code : '',
                    'account_name' => $taxAccount ? $taxAccount->account_name : 'Tax Input Account',
                    'label' => "Tax Input: " . ($taxModel ? $taxModel->tax_name : 'Tax'),
                    'debit' => floatval($item['tax_amount']),
                    'credit' => 0.0,
                ];
            }
        }

        return $preview;
    }
}
