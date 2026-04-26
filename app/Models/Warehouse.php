<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'warehouse_code',
        'warehouse_name',
        'warehouse_short_name',
        'warehouse_address',
        'shipment_type',
        'is_buy_resupply',
        'is_active',
    ];

    protected $casts = [
        'is_buy_resupply' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function locations()
    {
        return $this->hasMany(WarehouseLocation::class);
    }
}
