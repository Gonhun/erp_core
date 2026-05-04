<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'customer_code',
        'customer_name',
        'customer_address',
        'customer_region',
        'customer_state',
        'customer_postal_code',
        'customer_npwp',
        'customer_nik',
        'tax_name',
        'tax_address',
        'customer_phone',
        'customer_mobile',
        'customer_email',
        'customer_website',
        'is_individual',
        'is_company',
        'is_ppn',
        'is_pkp',
        'is_active',
        'credit_limit',
    ];

    protected $casts = [
        'is_individual' => 'boolean',
        'is_company' => 'boolean',
        'is_ppn' => 'boolean',
        'is_pkp' => 'boolean',
        'is_active' => 'boolean',
        'credit_limit' => 'decimal:2',
    ];

    public function accounting()
    {
        return $this->hasOne(CustomerAccounting::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(CustomerBankAccount::class);
    }

    public function contacts()
    {
        return $this->hasMany(CustomerContact::class);
    }
}
