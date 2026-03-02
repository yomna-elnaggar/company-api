<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ── Company ──────────────────────────────────────────────────────
            'company_name'               => ['required', 'string', 'max:255'],
            'tax_id'                     => [
                'required',
                'string',
                'size:15',
                'regex:/^3\d{13}3$/',
                'unique:companies,tax_id',
            ],
            'commercial_record'          => ['nullable', 'string', 'max:255'],
            'national_id'                => ['nullable', 'string', 'max:255'],
            'commerce_letter'            => ['nullable', 'string', 'max:255'],
            'contact_id'                 => ['nullable', 'string', 'max:255'],
            'electronic_contract_website'=> ['nullable', 'url', 'max:255'],
            'city_id'                    => ['nullable', 'integer', 'exists:cities,id'],
            'sale_person_id'             => ['nullable', 'integer', 'exists:sale_people,id'],

            // ── Company User ──────────────────────────────────────────────────
            'user.name'                  => ['required', 'string', 'max:255'],
            'user.email'                 => ['required', 'email', 'max:255', 'unique:users,email'],
            'user.username'              => ['required', 'string', 'max:255', 'unique:users,username'],
            'user.password'              => ['required', 'string', 'min:8'],
            'user.mobile'               => ['required', 'string', 'max:20', 'unique:users,mobile'],
            'user.iqama'                 => ['nullable', 'string', 'max:255'],
            'user.image'                 => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'tax_id.required'         => 'Tax ID is required.',
            'tax_id.size'             => 'Tax ID must be exactly 15 digits.',
            'tax_id.regex'            => 'Tax ID must be 15 digits and start/end with 3.',
            'tax_id.unique'           => 'This Tax ID is already registered.',
            'city_id.exists'          => 'The selected city does not exist.',
            'user.email.unique'       => 'This email is already taken.',
            'user.mobile.unique'      => 'This mobile number is already taken.',
            'user.username.unique'    => 'This username is already taken.',
        ];
    }

    public function attributes(): array
    {
        return [
            'user.name'     => 'user name',
            'user.email'    => 'user email',
            'user.username' => 'username',
            'user.password' => 'user password',
            'user.mobile'   => 'mobile number',
            'user.iqama'    => 'iqama number',
            'user.image'    => 'user image',
        ];
    }
}
