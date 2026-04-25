<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupAccount extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'group_accounts_name',
    ];

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}
