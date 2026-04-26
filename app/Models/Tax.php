<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'tax_name',
        'tax_computation',
        'tax_type',
        'tax_scope',
        'tax_amount',
        'is_ppn',
        'is_active',
    ];

    protected $casts = [
        'is_ppn' => 'boolean',
        'is_active' => 'boolean',
        'tax_amount' => 'double',
        'tax_computation' => 'integer',
    ];

    public function setups()
    {
        return $this->hasMany(TaxSetup::class);
    }

    public function invoiceDefinitions()
    {
        return $this->hasMany(TaxInvoiceDefinition::class);
    }

    public function refundDefinitions()
    {
        return $this->hasMany(TaxRefundDefinition::class);
    }
}
