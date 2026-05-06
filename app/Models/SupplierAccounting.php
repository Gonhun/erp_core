<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierAccounting extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'account_receivable',
        'account_payable',
    ];

    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function receivableAccount() { return $this->belongsTo(Account::class, 'account_receivable'); }
    public function payableAccount() { return $this->belongsTo(Account::class, 'account_payable'); }
}
