<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Translatable\HasTranslations;

class CompanySetting extends Model
{
    use HasFactory, SoftDeletes, HasUuids, HasTranslations;

    protected $table = 'company_settings';

    public $translatable = ['vehicle_receiving_terms'];
    protected $fillable = [
        'company_id',
        'is_meter_number_required',
        'is_meter_image_required',
        'is_code_changeable',
        'is_code_generator',
        'code',
        'login_with_otp',
        'vehicle_limit_type',
        'vehicle_balance_min',
        'vehicles_can_use_fuel_balance',
        'type_of_wallet',
        'auto_balance',
        'fuel_pull_limit',
        'fuel_pull_limit_days',
        'wash_count',
        'vehicle_receiving_terms',
        'allow_multiple_assignments',
    ];

    protected $casts = [
        'is_meter_number_required' => 'boolean',
        'is_meter_image_required' => 'boolean',
        'is_code_changeable' => 'boolean',
        'is_code_generator' => 'boolean',
        'login_with_otp' => 'boolean',
        'vehicles_can_use_fuel_balance' => 'boolean',
        'auto_balance' => 'boolean',
        'allow_multiple_assignments' => 'boolean',
        'vehicle_balance_min'        => 'decimal:2',
        'fuel_pull_limit'            => 'decimal:2',
        'fuel_pull_limit_days'       => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
