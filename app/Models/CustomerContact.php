<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerContact extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'contact_type',
        'customer_contact_address',
        'customer_contact_region',
        'customer_contact_state',
        'customer_contact_postal_code',
        'customer_contact_email',
        'customer_contact_phone',
        'customer_contact_mobile',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
