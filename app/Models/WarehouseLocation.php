<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseLocation extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'location_code',
        'location_name',
        'warehouse_id',
        'location_type',
        'is_parent',
        'parent_location',
        'is_negative_stock',
        'is_scrap_location',
        'is_return_location',
        'is_replenish',
        'inventory_frequency',
        'location_note',
        'is_active',
    ];

    protected $casts = [
        'is_parent' => 'boolean',
        'is_negative_stock' => 'boolean',
        'is_scrap_location' => 'boolean',
        'is_return_location' => 'boolean',
        'is_replenish' => 'boolean',
        'is_active' => 'boolean',
        'inventory_frequency' => 'integer',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function category()
    {
        return $this->belongsTo(LocationCategory::class, 'location_type');
    }

    public function parent()
    {
        return $this->belongsTo(WarehouseLocation::class, 'parent_location');
    }

    public function children()
    {
        return $this->hasMany(WarehouseLocation::class, 'parent_location');
    }
}
