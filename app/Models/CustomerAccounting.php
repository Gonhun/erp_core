<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerAccounting extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'account_receivable',
        'account_payable',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function receivableAccount()
    {
        return $this->belongsTo(Account::class, 'account_receivable');
    }

    public function payableAccount()
    {
        return $this->belongsTo(Account::class, 'account_payable');
    }
}
