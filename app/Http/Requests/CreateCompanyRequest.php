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
            'image'                      => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'commerce_letter'            => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:2048'],
            'commercial_record'          => ['nullable', 'file', 'mimes:pdf', 'max:2048'],
            'national_id'                => ['nullable', 'string', 'max:255'],
            'contact_id'                 => ['nullable', 'string', 'max:255'],
            'electronic_contract_website'=> ['nullable', 'url', 'max:255'],
            'city_id'                    => ['nullable', 'string'],
            'sale_person_id'             => ['nullable', 'string'],


            // ── User / Manager (for Microservice) ────────────────────────────
            'name'                       => ['required', 'string', 'max:255'],
            'email'                      => ['required', 'email', 'max:255'],
            'mobile'                     => ['required', 'string', 'max:20'],
            'password'                   => ['required', 'confirmed', 'string', 'min:8'],
            'password_confirmation'      => ['required', 'string', 'min:8'],
            'username'                   => ['nullable', 'string', 'max:255'],


            // ── Package ──────────────────────────────────────────────────────
            'package_id'                 => ['nullable', 'string'],
            'vehicle_number'             => ['nullable', 'integer', 'min:1'],
            'price'                      => ['nullable', 'numeric'],
            'price_with_tax'             => ['nullable', 'numeric'],
            'start_date'                 => ['nullable', 'date'],
            'end_date'                   => ['nullable', 'date', 'after_or_equal:start_date'],
            'payment_status'             => ['nullable', 'string'],
        ];
    }

}
