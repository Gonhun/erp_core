<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasUuids;

    protected $fillable = [
        'payment_number',
        'supplier_id',
        'customer_id',
        'payment_date',
        'currency',
        'amount',
        'payment_method',
        'journal_account_id',
        'reference',
        'notes',
        'status',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function journalAccount()
    {
        return $this->belongsTo(Account::class, 'journal_account_id');
    }

    public function allocations()
    {
        return $this->hasMany(PaymentAllocation::class);
    }
}
