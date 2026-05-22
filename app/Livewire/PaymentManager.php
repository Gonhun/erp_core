<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\VendorBill;
use App\Models\Supplier;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalItem;
use Illuminate\Support\Facades\DB;

class PaymentManager extends Component
{
    public $payments;
    public $isCreating = false;
    public $editingPaymentId = null;
    public $iteration = 0;

    // Header fields
    public $payment_number;
    public $supplier_id = '';
    public $payment_date;
    public $currency = 'IDR';
    public $amount = 0;
    public $payment_method = 'Bank Transfer';
    public $journal_account_id = '';
    public $reference;
    public $notes;
    public $status = 'draft';

    // List of outstanding bills to allocate
    public $allocations = []; // Array of [vendor_bill_id, bill_number, total_amount, amount_due, amount_allocated]

    // Master Dropdowns
    public $suppliersList = [];
    public $accountsList = [];

    protected $rules = [
        'supplier_id' => 'required',
        'payment_date' => 'required|date',
        'currency' => 'required|string|max:10',
        'amount' => 'required|numeric|min:0.01',
        'payment_method' => 'required|string',
        'journal_account_id' => 'required',
    ];

    public function mount()
    {
        $this->suppliersList = Supplier::where('is_active', true)->get();
        // Asset accounts (like Cash, Bank) usually start with '1'
        $this->accountsList = Account::where('is_active', true)->orderBy('account_code')->get();
        $this->payment_date = date('Y-m-d');
        
        // Find default Bank or Cash account
        $defaultBank = Account::where('account_name', 'like', '%Bank%')
            ->orWhere('account_name', 'like', '%Cash%')
            ->first() ?: Account::first();
        
        if ($defaultBank) {
            $this->journal_account_id = $defaultBank->id;
        }
    }

    public function render()
    {
        $this->payments = Payment::with(['supplier', 'journalAccount'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.payment-manager')->layout('layouts.app');
    }

    public function updatedSupplierId($value)
    {
        $this->loadOutstandingBills();
    }

    public function loadOutstandingBills()
    {
        $this->allocations = [];
        if (empty($this->supplier_id)) return;

        // Fetch posted bills for this supplier that have outstanding balance
        $bills = VendorBill::where('supplier_id', $this->supplier_id)
            ->where('status', 'posted')
            ->get();

        foreach ($bills as $bill) {
            $due = $bill->amount_due;
            if ($due > 0) {
                $this->allocations[] = [
                    'vendor_bill_id' => $bill->id,
                    'bill_number' => $bill->bill_number,
                    'total_amount' => $bill->total_amount,
                    'amount_due' => $due,
                    'amount_allocated' => 0,
                ];
            }
        }
    }

    public function autoAllocate()
    {
        $remainingPayment = floatval($this->amount);
        
        foreach ($this->allocations as $index => $alloc) {
            $due = floatval($alloc['amount_due']);
            if ($remainingPayment >= $due) {
                $this->allocations[$index]['amount_allocated'] = $due;
                $remainingPayment -= $due;
            } else {
                $this->allocations[$index]['amount_allocated'] = $remainingPayment;
                $remainingPayment = 0;
            }
        }
    }

    public function createNew()
    {
        $this->resetForm();
        $this->isCreating = true;
        $this->editingPaymentId = null;
        $this->iteration++;
    }

    public function resetForm()
    {
        $this->payment_number = 'Draft Payment';
        $this->supplier_id = '';
        $this->payment_date = date('Y-m-d');
        $this->currency = 'IDR';
        $this->amount = 0;
        $this->payment_method = 'Bank Transfer';
        
        $defaultBank = Account::where('account_name', 'like', '%Bank%')
            ->orWhere('account_name', 'like', '%Cash%')
            ->first() ?: Account::first();
        if ($defaultBank) {
            $this->journal_account_id = $defaultBank->id;
        }

        $this->reference = '';
        $this->notes = '';
        $this->status = 'draft';
        $this->allocations = [];
    }

    public function edit($id)
    {
        $payment = Payment::with(['supplier', 'journalAccount', 'allocations.bill'])->findOrFail($id);
        $this->editingPaymentId = $id;
        $this->isCreating = false;

        $this->payment_number = $payment->payment_number;
        $this->supplier_id = $payment->supplier_id;
        $this->payment_date = $payment->payment_date->format('Y-m-d');
        $this->currency = $payment->currency;
        $this->amount = $payment->amount;
        $this->payment_method = $payment->payment_method;
        $this->journal_account_id = $payment->journal_account_id;
        $this->reference = $payment->reference;
        $this->notes = $payment->notes;
        $this->status = $payment->status;

        // Load allocations
        $this->allocations = [];
        foreach ($payment->allocations as $alloc) {
            $this->allocations[] = [
                'vendor_bill_id' => $alloc->vendor_bill_id,
                'bill_number' => $alloc->bill->bill_number,
                'total_amount' => $alloc->bill->total_amount,
                'amount_due' => $alloc->bill->amount_due + $alloc->amount_allocated, // original amount due before this payment
                'amount_allocated' => $alloc->amount_allocated,
            ];
        }

        $this->iteration++;
    }

    public function save()
    {
        $this->validate();

        // Validate allocation total matches or is below total amount
        $totalAllocated = 0;
        foreach ($this->allocations as $alloc) {
            $totalAllocated += floatval($alloc['amount_allocated']);
            if (floatval($alloc['amount_allocated']) > floatval($alloc['amount_due'])) {
                session()->flash('error', "Allocation for bill {$alloc['bill_number']} cannot exceed the amount due.");
                return;
            }
        }

        if ($totalAllocated > floatval($this->amount)) {
            session()->flash('error', 'Total allocated amount cannot exceed the payment amount.');
            return;
        }

        try {
            DB::transaction(function () {
                $isNew = empty($this->editingPaymentId);

                if ($isNew) {
                    $year = date('Y', strtotime($this->payment_date));
                    $month = date('m', strtotime($this->payment_date));
                    $prefix = "PAY/{$year}/{$month}/";
                    
                    $lastPayment = Payment::where('payment_number', 'like', $prefix . '%')
                        ->orderBy('payment_number', 'desc')
                        ->first();
                    
                    if ($lastPayment) {
                        $lastNum = intval(substr($lastPayment->payment_number, -4));
                        $newNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
                    } else {
                        $newNum = '0001';
                    }
                    $this->payment_number = $prefix . $newNum;

                    $payment = Payment::create([
                        'payment_number' => $this->payment_number,
                        'supplier_id' => $this->supplier_id,
                        'payment_date' => $this->payment_date,
                        'currency' => $this->currency,
                        'amount' => $this->amount,
                        'payment_method' => $this->payment_method,
                        'journal_account_id' => $this->journal_account_id,
                        'reference' => $this->reference,
                        'notes' => $this->notes,
                        'status' => 'draft',
                    ]);
                } else {
                    $payment = Payment::findOrFail($this->editingPaymentId);
                    
                    if ($payment->status !== 'draft') {
                        throw new \Exception('Only draft payments can be modified.');
                    }

                    $payment->update([
                        'supplier_id' => $this->supplier_id,
                        'payment_date' => $this->payment_date,
                        'currency' => $this->currency,
                        'amount' => $this->amount,
                        'payment_method' => $this->payment_method,
                        'journal_account_id' => $this->journal_account_id,
                        'reference' => $this->reference,
                        'notes' => $this->notes,
                    ]);

                    // Delete allocations to recreate
                    $payment->allocations()->delete();
                }

                // Save allocations
                foreach ($this->allocations as $alloc) {
                    if (floatval($alloc['amount_allocated']) > 0) {
                        PaymentAllocation::create([
                            'payment_id' => $payment->id,
                            'vendor_bill_id' => $alloc['vendor_bill_id'],
                            'amount_allocated' => $alloc['amount_allocated'],
                        ]);
                    }
                }

                $this->editingPaymentId = $payment->id;
                $this->isCreating = false;
            });

            session()->flash('message', 'Payment registration saved as draft.');
            $this->iteration++;
        } catch (\Exception $e) {
            session()->flash('error', 'Error while saving payment: ' . $e->getMessage());
        }
    }

    public function postPayment()
    {
        if (empty($this->editingPaymentId)) return;

        $payment = Payment::findOrFail($this->editingPaymentId);
        if ($payment->status !== 'draft') {
            session()->flash('error', 'Only draft payments can be posted.');
            return;
        }

        try {
            DB::transaction(function () use ($payment) {
                // Find default AP account from supplier accounting setup
                $supplierAcc = \App\Models\SupplierAccounting::where('supplier_id', $payment->supplier_id)->first();
                $apAccount = $supplierAcc ? $supplierAcc->payableAccount : null;

                if (!$apAccount) {
                    $apAccount = Account::where('account_code', 'like', '2%')
                        ->orWhere('account_name', 'like', '%Payable%')
                        ->first();
                }

                if (!$apAccount) {
                    throw new \Exception('No Accounts Payable GL account configured for this vendor.');
                }

                // Create Journal Entry
                $year = date('Y', strtotime($payment->payment_date));
                $month = date('m', strtotime($payment->payment_date));
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
                    'entry_date' => $payment->payment_date,
                    'reference' => $payment->payment_number,
                    'notes' => 'Payment validation for ' . $payment->payment_number,
                    'status' => 'posted',
                ]);

                // Debit Accounts Payable (liability decreases!)
                JournalItem::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $apAccount->id,
                    'partner_id' => $payment->supplier_id,
                    'partner_type' => 'supplier',
                    'debit' => $payment->amount,
                    'credit' => 0,
                    'name' => "Accounts Payable Payment - " . $payment->payment_number,
                ]);

                // Credit Cash/Bank (asset decreases!)
                JournalItem::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $payment->journal_account_id,
                    'partner_id' => $payment->supplier_id,
                    'partner_type' => 'supplier',
                    'debit' => 0,
                    'credit' => $payment->amount,
                    'name' => "Bank Transfer / Cash payout - " . $payment->payment_number,
                ]);

                // Set payment status to posted
                $payment->update(['status' => 'posted']);
                $this->status = 'posted';

                // Auto update allocated bills to 'paid' if amount due is 0
                foreach ($payment->allocations as $alloc) {
                    $bill = $alloc->bill;
                    if ($bill->amount_due <= 0) {
                        $bill->update(['status' => 'paid']);
                    }
                }
            });

            session()->flash('message', 'Payment posted successfully and vendor ledger updated.');
            $this->iteration++;
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to post payment: ' . $e->getMessage());
        }
    }

    public function cancelPayment()
    {
        if (empty($this->editingPaymentId)) return;

        $payment = Payment::findOrFail($this->editingPaymentId);
        if ($payment->status !== 'posted') {
            session()->flash('error', 'Only posted payments can be cancelled.');
            return;
        }

        try {
            DB::transaction(function () use ($payment) {
                // Find and delete matching journal entry
                $je = JournalEntry::where('reference', $payment->payment_number)->first();
                if ($je) {
                    $je->delete();
                }

                // Restore any allocated bills to 'posted' status if they were marked 'paid'
                foreach ($payment->allocations as $alloc) {
                    $bill = $alloc->bill;
                    if ($bill->status === 'paid') {
                        $bill->update(['status' => 'posted']);
                    }
                }

                $payment->update(['status' => 'cancel']);
                $this->status = 'cancel';
            });

            session()->flash('message', 'Payment cancelled and allocated bills recalculated.');
            $this->iteration++;
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to cancel payment: ' . $e->getMessage());
        }
    }

    public function discard()
    {
        $this->isCreating = false;
        $this->editingPaymentId = null;
        $this->iteration++;
    }
}
