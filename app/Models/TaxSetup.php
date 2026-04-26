<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxSetup extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'taxes_setup';

    protected $fillable = [
        'tax_id',
        'tax_label',
        'tax_country',
        'is_included_price',
    ];

    protected $casts = [
        'is_included_price' => 'boolean',
    ];

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }
}
