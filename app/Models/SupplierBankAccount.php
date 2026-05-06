<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierBankAccount extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'bank',
        'bank_account_name',
        'bank_account_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function supplier() { return $this->belongsTo(Supplier::class); }
}
