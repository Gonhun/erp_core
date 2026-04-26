<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Uom extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'uom_name',
        'uom_category_id',
        'uom_type',
        'ratio',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'ratio' => 'decimal:4',
    ];

    public function category()
    {
        return $this->belongsTo(UomCategory::class, 'uom_category_id');
    }
}
