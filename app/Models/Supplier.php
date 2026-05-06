<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'supplier_code',
        'supplier_name',
        'supplier_address',
        'supplier_region',
        'supplier_state',
        'supplier_postal_code',
        'supplier_phone',
        'supplier_email',
        'supplier_website',
        'supplier_npwp',
        'supplier_nik',
        'tax_name',
        'tax_address',
        'is_pkp',
        'is_ppn',
        'is_individual',
        'is_active',
    ];

    protected $casts = [
        'is_pkp' => 'boolean',
        'is_ppn' => 'boolean',
        'is_individual' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function accounting() { return $this->hasOne(SupplierAccounting::class); }
    public function bankAccounts() { return $this->hasMany(SupplierBankAccount::class); }
    public function contacts() { return $this->hasMany(SupplierContact::class); }
}
