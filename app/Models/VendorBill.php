<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorBill extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'bill_number',
        'vendor_bill_number',
        'purchase_order_id',
        'supplier_id',
        'bill_date',
        'due_date',
        'currency',
        'untaxed_amount',
        'tax_amount',
        'total_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'bill_date' => 'date',
        'due_date' => 'date',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(VendorBillItem::class);
    }

    public function allocations()
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    // Calculated remaining amount to be paid
    public function getAmountDueAttribute()
    {
        $paid = $this->allocations()->sum('amount_allocated');
        return max(0, $this->total_amount - $paid);
    }
}
