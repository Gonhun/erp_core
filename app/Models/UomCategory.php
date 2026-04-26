<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UomCategory extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'category_name',
        'base_uom_name',
        'base_uom',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
