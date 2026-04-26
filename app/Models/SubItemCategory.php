<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubItemCategory extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'sub_category_name',
        'item_category_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id');
    }
}
