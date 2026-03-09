<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_name' => $this->company_name,
            'trade_name' => $this->trade_name,
            'tax_id' => $this->tax_id,
            'commercial_record' => $this->commercial_record,
            'national_id' => $this->national_id,
            'contact_id' => $this->contact_id,
            'commerce_letter' => $this->commerce_letter,
            'electronic_contract_website' => $this->electronic_contract_website,
            'active' => $this->active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // From related models/services (Only load them in 'show' method to avoid N+1 HTTP timeouts)
            'city_id' => $this->city_id,
            'city' => $this->when($request->route()->getActionMethod() === 'show', function () {
            return $this->city; }),
            'active_package' => $this->when($request->route()->getActionMethod() === 'show', function () {
            return $this->active_package; }),
            'packages' => $this->when($request->route()->getActionMethod() === 'show', function () {
            return $this->packages; }),
            'user' => $this->whenLoaded('user'),
            'settings' => $this->whenLoaded('companySetting'),

        ];
    }
}
