<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocationCategory extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'location_categories';

    protected $fillable = [
        'location_name',
    ];

    public function locations()
    {
        return $this->hasMany(WarehouseLocation::class, 'location_type');
    }
}
