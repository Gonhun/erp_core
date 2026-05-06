<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'product_code',
        'product_name',
        'product_type_id',
        'invoicing_policy',
        'brand_id',
        'uom_category_id',
        'purchase_uom_category_id',
        'item_category_id',
        'sub_item_id',
        'part_number',
        'product_notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function productType() { return $this->belongsTo(ProductType::class, 'product_type_id'); }
    public function brand() { return $this->belongsTo(Brand::class, 'brand_id'); }
    public function uomCategory() { return $this->belongsTo(UomCategory::class, 'uom_category_id'); }
    public function purchaseUomCategory() { return $this->belongsTo(UomCategory::class, 'purchase_uom_category_id'); }
    public function itemCategory() { return $this->belongsTo(ItemCategory::class, 'item_category_id'); }
    public function subItem() { return $this->belongsTo(SubItemCategory::class, 'sub_item_id'); }
    public function substitutes() { return $this->hasMany(ProductSubstitute::class, 'product_id'); }
}
