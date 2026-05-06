<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'po_number',
        'supplier_id',
        'order_date',
        'order_deadline',
        'expected_arrival',
        'currency',
        'warehouse_id',
        'status',
        'untaxed_amount',
        'tax_amount',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'order_date' => 'date',
        'order_deadline' => 'date',
        'expected_arrival' => 'date',
        'untaxed_amount' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'total_amount' => 'decimal:4',
    ];

    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function items() { return $this->hasMany(PurchaseOrderItem::class); }
}
