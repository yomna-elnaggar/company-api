<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'wallet_number',
        'balance',
        'active',
        'company_id',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'active'  => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
