<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id'              => 'nullable|string', 
            'title'           => 'required|string|max:255',
            'company_id'      => 'required|string|exists:companies,id',
            'city_id'         => 'required|string',
            'address'         => 'nullable|string',
            'phone'           => 'nullable|string',
            'email'           => 'nullable|email',
            'manager_name'    => 'nullable|string',
            'manager_contact' => 'nullable|string',
            'latitude'        => 'nullable|numeric',
            'longitude'       => 'nullable|numeric',
            'active'          => 'boolean',
        ];
    }
}
