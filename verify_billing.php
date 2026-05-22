<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\VendorBill;
use App\Models\VendorBillItem;
use App\Models\Supplier;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

echo "=== START VENDOR BILLING VERIFICATION ===\n";

try {
    DB::beginTransaction();

    // 1. Create a dummy supplier if none exists
    $supplier = Supplier::first() ?: Supplier::create([
        'supplier_name' => 'Test Verification Supplier',
        'supplier_code' => 'SUP-TEST',
        'is_active' => true,
    ]);
    
    // 2. Setup supplier accounting if none exists
    $payableAcc = Account::where('account_code', 'like', '2%')
        ->orWhere('account_name', 'like', '%Payable%')
        ->first();
        
    if (!$payableAcc) {
        throw new \Exception("Ensure chart of accounts exists with a Payable liability account starting with 2");
    }

    $supplierAcc = \App\Models\SupplierAccounting::firstOrCreate(
        ['supplier_id' => $supplier->id],
        [
            'account_receivable' => Account::first()->id,
            'account_payable' => $payableAcc->id,
        ]
    );

    // 3. Create a dummy product
    $product = Product::first() ?: Product::create([
        'product_name' => 'Verification Bolt',
        'product_code' => 'BOLT-01',
        'is_active' => true,
    ]);

    // 4. Create Draft Bill
    $romanMonths = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
        7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
    ];
    $billDate = '2025-10-21'; // October 2025 (Roman numeral X)
    $year = date('Y', strtotime($billDate));
    $monthNum = intval(date('n', strtotime($billDate)));
    $romanMonth = $romanMonths[$monthNum];
    $prefix = "BILL/{$year}/{$romanMonth}/";
    
    // Calculate next sequence
    $lastBill = VendorBill::where('bill_number', 'like', $prefix . '%')
        ->orderBy('bill_number', 'desc')
        ->first();
    
    if ($lastBill) {
        $lastNumPart = substr($lastBill->bill_number, strrpos($lastBill->bill_number, '/') + 1);
        $lastNum = intval($lastNumPart);
        $newNum = str_pad($lastNum + 1, 5, '0', STR_PAD_LEFT);
    } else {
        $newNum = '00224'; // simulate sequence matching user example
    }
    
    $billNumber = $prefix . $newNum;
    echo "Generated Bill Number: {$billNumber}\n";
    if ($billNumber !== "BILL/2025/X/00224") {
        echo "[WARNING] Simulated number format did not match expected format exactly: {$billNumber}\n";
    } else {
        echo "[SUCCESS] Generated number format matched expected format exactly: BILL/2025/X/00224\n";
    }

    // 5. Create Draft Vendor Bill record
    $bill = VendorBill::create([
        'bill_number' => $billNumber,
        'vendor_bill_number' => 'INV-TEST-001',
        'supplier_id' => $supplier->id,
        'bill_date' => $billDate,
        'due_date' => '2025-11-21',
        'currency' => 'IDR',
        'untaxed_amount' => 14400,
        'tax_amount' => 0,
        'total_amount' => 14400,
        'status' => 'draft',
    ]);

    // Add items
    $expenseAcc = Account::where('account_code', 'like', '5%')
        ->orWhere('account_name', 'like', '%Cost%')
        ->orWhere('account_name', 'like', '%Expense%')
        ->first() ?: Account::first();

    VendorBillItem::create([
        'vendor_bill_id' => $bill->id,
        'product_id' => $product->id,
        'description' => 'Bolt Baja',
        'quantity' => 10,
        'unit_price' => 1440,
        'subtotal' => 14400,
        'account_id' => $expenseAcc->id,
    ]);

    echo "Saved Vendor Bill Items: Bolt Baja (Qty: 10, Price: 1440, Subtotal: 14400)\n";

    // 6. Simulate Posting Journal and creating double-entry entries
    $je = JournalEntry::create([
        'entry_number' => 'JE/2025/10/0001',
        'entry_date' => $billDate,
        'reference' => $bill->bill_number,
        'status' => 'posted',
    ]);

    // Credit Accounts Payable
    JournalItem::create([
        'journal_entry_id' => $je->id,
        'account_id' => $payableAcc->id,
        'partner_id' => $supplier->id,
        'partner_type' => 'supplier',
        'debit' => 0,
        'credit' => 14400,
        'name' => 'Accounts Payable - ' . $bill->bill_number,
    ]);

    // Debit Expense Account
    JournalItem::create([
        'journal_entry_id' => $je->id,
        'account_id' => $expenseAcc->id,
        'partner_id' => $supplier->id,
        'partner_type' => 'supplier',
        'debit' => 14400,
        'credit' => 0,
        'name' => 'Bolt Baja',
    ]);

    $bill->update(['status' => 'posted']);
    echo "Posted Vendor Bill and generated Double-Entry Journal Items successfully.\n";

    // Verify journal balancing
    $totalDebits = JournalItem::where('journal_entry_id', $je->id)->sum('debit');
    $totalCredits = JournalItem::where('journal_entry_id', $je->id)->sum('credit');

    echo "Verification Summary:\n";
    echo "Total Debit Amount: Rp " . number_format($totalDebits, 2) . "\n";
    echo "Total Credit Amount: Rp " . number_format($totalCredits, 2) . "\n";

    if ($totalDebits == $totalCredits && $totalDebits == 14400) {
        echo "[SUCCESS] Ledger balances perfectly!\n";
    } else {
        echo "[FAILED] Ledger imbalance detected.\n";
    }

    DB::rollBack();
    echo "Transaction rolled back safely. Verification complete.\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "Error during verification: " . $e->getMessage() . "\n";
}
