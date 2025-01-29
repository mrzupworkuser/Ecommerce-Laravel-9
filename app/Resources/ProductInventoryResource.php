<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductInventoryResource extends JsonResource
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
            'inventory_type' => $this->inventory_type,
            'quantity' => $this->quantity,
            'assets' => AssetResource::collection($this->whenLoaded('assets')),
        ];
    }
}
