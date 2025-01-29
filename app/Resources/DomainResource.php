<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DomainResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->getKey(),
            'domain' => $this->domain,
            'company' => $this->company,
            'is_primary' => $this->is_primary,
            'is_fallback' => $this->is_fallback,
            'location_name' => $this->location_name,
            'website' => $this->website,
            'primary_industry' => $this->primary_industry,
            'email' => $this->email,
            'phone' => $this->phone,
            'dial_code' => $this->dial_code,
            'company_description' => $this->company_description,
            'address_1' => $this->address_1,
            'address_2' => $this->address_2,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'country' => $this->country,
            'timezone_iso3' => $this->timezone_iso3,
            'currency_iso3' => $this->currency_iso3,
            'language_iso2' => $this->language_iso2,
            'order_number_format' => $this->order_number_format,
            'order_number_prefix' => $this->order_number_prefix,
            'date_format' => $this->date_format,
            'time_format' => $this->time_format,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'week_starts_on' => $this->data['week_starts_on'] ?? 'monday',
            'business_hours' => $this->data['business_hours'] ?? [],
        ];
    }
}
