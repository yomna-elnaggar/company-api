<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyPackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Since data might come from an API as an array, we handle both object and array access
        $data = is_array($this->resource) ? $this->resource : $this->resource->toArray();

        return [
            'id'             => $data['id'] ?? null,
            'package_id'     => $data['package_id'] ?? null,
            'package_name'   => $data['package']['title'] ?? ($data['package_name'] ?? null),
            'subscribed_at'  => $data['subscribed_at'] ?? null,
            'expires_at'     => $data['expires_at'] ?? null,
            'num_of_cars'    => $data['num_of_cars'] ?? 0,
            'price'          => $data['price'] ?? 0,
            'price_with_tax' => $data['price_with_tax'] ?? 0,
            'payment_status' => $data['payment_status'] ?? 'pending',
            'active'         => $data['active'] ?? false,
        ];
    }
}
