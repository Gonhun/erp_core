<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemCategory extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'category_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function subCategories()
    {
        return $this->hasMany(SubItemCategory::class, 'item_category_id');
    }
}
