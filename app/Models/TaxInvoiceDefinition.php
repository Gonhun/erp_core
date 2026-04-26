<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxInvoiceDefinition extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'taxes_invoice_definition';

    protected $fillable = [
        'tax_id',
        'def_amount',
        'account_id',
    ];

    protected $casts = [
        'def_amount' => 'decimal:2',
    ];

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
