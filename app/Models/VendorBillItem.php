<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class VendorBillItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'vendor_bill_id',
        'purchase_order_item_id',
        'product_id',
        'description',
        'quantity',
        'unit_price',
        'discount',
        'tax_id',
        'tax_amount',
        'subtotal',
        'account_id',
    ];

    public function bill()
    {
        return $this->belongsTo(VendorBill::class, 'vendor_bill_id');
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
