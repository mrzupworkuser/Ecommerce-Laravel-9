<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
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
            'id' => $this->id,
            'title' => $this->title,
            'code' => $this->code,
            'code_type' => $this->code_type,
            'amount' => $this->amount,
            'status' => $this->status,
            'start_at' => $this->start_at,
            'expired_at' => $this->expired_at,
            'created_by' => $this->created_by,
            'settings' => $this->settings,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'products' => ProductResource::collection($this->products),
            'apply_once_per_type' => $this->apply_once_per_type,
            'max_redemption_time' => $this->max_redemption_time,
        ];
    }
}
