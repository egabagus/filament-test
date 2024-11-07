<?php

namespace App\Filament\Resources\ItemResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return $this->resource->toArray();
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'desc' => $this->desc,
            'category_id' => $this->category_id,
            'price' => $this->price,
            'stock' => $this->stock,
            'status' => $this->status
        ];
    }
}
