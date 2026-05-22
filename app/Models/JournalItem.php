<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class JournalItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'partner_id',
        'partner_type',
        'debit',
        'credit',
        'name',
    ];

    public function entry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function partner()
    {
        if ($this->partner_type === 'supplier') {
            return $this->belongsTo(Supplier::class, 'partner_id');
        } elseif ($this->partner_type === 'customer') {
            return $this->belongsTo(Customer::class, 'partner_id');
        }
        return null;
    }
}
