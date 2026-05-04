<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSubstitute extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'product_id',
        'substitute_product_id',
        'priority',
        'is_active',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function substituteProduct()
    {
        return $this->belongsTo(Product::class, 'substitute_product_id');
    }
}
