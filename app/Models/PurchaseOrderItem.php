<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderItem extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'description',
        'quantity',
        'uom_category_id',
        'unit_price',
        'discount',
        'tax_id',
        'tax_amount',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'discount' => 'decimal:2',
        'tax_amount' => 'decimal:4',
        'subtotal' => 'decimal:4',
    ];

    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function uomCategory() { return $this->belongsTo(UomCategory::class, 'uom_category_id'); }
    public function tax() { return $this->belongsTo(Tax::class); }
}
