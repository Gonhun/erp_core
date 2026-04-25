<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'account_code',
        'account_name',
        'group_account_id',
        'balance_type',
        'account_level',
        'is_reconciliation',
        'reconciliation_type',
        'account_currency',
        'is_parent',
        'parent_id',
        'is_active',
    ];

    public function groupAccount()
    {
        return $this->belongsTo(GroupAccount::class);
    }

    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }
}
