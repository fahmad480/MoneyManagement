<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
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
            'bank_id' => $this->bank_id,
            'bank' => new BankResource($this->whenLoaded('bank')),
            'card_name' => $this->card_name,
            'card_number' => $this->card_number,
            'transaction_limit' => $this->transaction_limit,
            'card_type' => $this->card_type,
            'card_form' => $this->card_form,
            'expiry_date' => $this->expiry_date?->format('Y-m-d'),
            'description' => $this->description,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
