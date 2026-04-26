<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductType extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'product_type_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
