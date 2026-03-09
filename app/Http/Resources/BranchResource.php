<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'title'           => $this->title,
            'address'         => $this->address,
            'phone'           => $this->phone,
            'email'           => $this->email,
            'manager_name'    => $this->manager_name,
            'manager_contact' => $this->manager_contact,
            'latitude'        => $this->latitude,
            'longitude'       => $this->longitude,
            'active'          => $this->active,
            'company_id'      => $this->company_id,
            'city_id'         => $this->city_id,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
            
            
            'company'         => $this->whenLoaded('company'),
            'city'            => $this->when($request->route()->getActionMethod() === 'show', function() { return $this->city; }), // Only load via api in 'show'
        ];
    }
}
