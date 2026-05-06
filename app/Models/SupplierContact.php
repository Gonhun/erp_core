<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierContact extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'contact_type',
        'contact_name',
        'supplier_contact_address',
        'supplier_contact_region',
        'supplier_contact_state',
        'supplier_contact_postal_code',
        'supplier_contact_email',
        'supplier_contact_phone',
        'supplier_contact_mobile',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function supplier() { return $this->belongsTo(Supplier::class); }
}
