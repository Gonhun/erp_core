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
        'uom_id',
        'purchase_uom_id',
        'item_category_id',
        'sub_item_id',
        'part_number',
        'product_notes',
    ];

    public function productType() { return $this->belongsTo(ProductType::class, 'product_type_id'); }
    public function brand() { return $this->belongsTo(Brand::class, 'brand_id'); }
    public function uom() { return $this->belongsTo(Uom::class, 'uom_id'); }
    public function purchaseUom() { return $this->belongsTo(Uom::class, 'purchase_uom_id'); }
    public function itemCategory() { return $this->belongsTo(ItemCategory::class, 'item_category_id'); }
    public function subItem() { return $this->belongsTo(SubItemCategory::class, 'sub_item_id'); }
}
