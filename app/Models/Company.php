<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_name',
        'tax_id',
        'commercial_record',
        'national_id',
        'commerce_letter',
        'contact_id',
        'electronic_contract_website',
        'city_id',
        'sale_person_id',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function salePerson()
    {
        return $this->belongsTo(SalePerson::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function settings()
    {
        return $this->hasOne(CompanySetting::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
}
