<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'card_id' => $this->card_id,
            'card' => new CardResource($this->whenLoaded('card')),
            'category_id' => $this->category_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'type' => $this->type,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'source' => $this->source,
            'to_bank_id' => $this->to_bank_id,
            'to_bank' => new BankResource($this->whenLoaded('toBank')),
            'reference_number' => $this->reference_number,
            'description' => $this->description,
            'notes' => $this->notes,
            'transaction_date' => $this->transaction_date?->format('Y-m-d H:i:s'),
            'charges' => TransactionChargeResource::collection($this->whenLoaded('charges')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
