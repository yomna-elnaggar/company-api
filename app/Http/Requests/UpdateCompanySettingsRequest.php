<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanySettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_meter_number_required'      => 'boolean',
            'is_meter_image_required'       => 'boolean',
            'is_code_changeable'            => 'boolean',
            'is_code_generator'             => 'boolean',
            'code'                          => 'nullable|string|size:4',
            'login_with_otp'                => 'boolean',
            'vehicle_limit_type'            => 'in:daily,weekly,monthly',
            'vehicle_balance_min'           => 'nullable|numeric',
            'vehicles_can_use_fuel_balance' => 'boolean',
            'type_of_wallet'                => 'in:branch,vehicle',
            'auto_balance'                  => 'boolean',
            'fuel_pull_limit'               => 'nullable|numeric',
            'fuel_pull_limit_days'          => 'nullable|integer',
            'wash_count'                    => 'nullable|integer',
            'vehicle_receiving_terms'       => 'nullable|string',
            'allow_multiple_assignments'    => 'boolean',
        ];
    }
}
