<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CrmUserStructureResource extends JsonResource
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
            'name' => $this->name,
            'phone' => $this->phone,
            'propertyType' => json_decode($this->property_type),
            'deal' => json_decode($this->deal),
            'room' => $this->room,
            'budget' => $this->budget,
            'email' => $this->email,
            'source' => $this->source,
            'contractNumber' => $this->contract_number,
            'comment' => $this->comment,
            'specialist' => $this->employee_id, 
            'status' => $this->status,
            'homes' => $this->homes->pluck('id')->toArray(),
            'files' => $this->files->pluck('path')->toArray(),
        ];
    }
}