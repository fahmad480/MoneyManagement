<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CardResource;
use App\Models\Card;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CardApiController extends Controller
{
    /**
     * Display a listing of cards.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        
        // Get banks that belong to the user
        $userBankIds = Bank::where('user_id', $request->user_id)->pluck('id');
        
        $cards = Card::whereIn('bank_id', $userBankIds)
            ->when($request->is_active, function ($query, $isActive) {
                $query->where('is_active', $isActive);
            })
            ->when($request->card_type, function ($query, $cardType) {
                $query->where('card_type', $cardType);
            })
            ->with('bank')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Cards retrieved successfully',
            'data' => CardResource::collection($cards),
            'meta' => [
                'current_page' => $cards->currentPage(),
                'last_page' => $cards->lastPage(),
                'per_page' => $cards->perPage(),
                'total' => $cards->total(),
            ]
        ]);
    }

    /**
     * Store a newly created card.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_id' => 'required|exists:banks,id',
            'card_name' => 'required|string|max:255',
            'card_number' => 'required|string|max:255',
            'transaction_limit' => 'required|numeric|min:0',
            'card_type' => 'required|in:debit,credit',
            'card_form' => 'required|in:physical,virtual',
            'expiry_date' => 'required|date',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify bank belongs to user
        $bank = Bank::where('id', $request->bank_id)
            ->where('user_id', $request->user_id)
            ->first();

        if (!$bank) {
            return response()->json([
                'success' => false,
                'message' => 'Bank not found or does not belong to you'
            ], 404);
        }

        $card = Card::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Card created successfully',
            'data' => new CardResource($card->load('bank'))
        ], 201);
    }

    /**
     * Display the specified card.
     */
    public function show(Request $request, $id)
    {
        $userBankIds = Bank::where('user_id', $request->user_id)->pluck('id');
        
        $card = Card::whereIn('bank_id', $userBankIds)
            ->with('bank')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Card retrieved successfully',
            'data' => new CardResource($card)
        ]);
    }

    /**
     * Update the specified card.
     */
    public function update(Request $request, $id)
    {
        $userBankIds = Bank::where('user_id', $request->user_id)->pluck('id');
        
        $card = Card::whereIn('bank_id', $userBankIds)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'bank_id' => 'exists:banks,id',
            'card_name' => 'string|max:255',
            'card_number' => 'string|max:255',
            'transaction_limit' => 'numeric|min:0',
            'card_type' => 'in:debit,credit',
            'card_form' => 'in:physical,virtual',
            'expiry_date' => 'date',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // If bank_id is being updated, verify it belongs to user
        if ($request->has('bank_id')) {
            $bank = Bank::where('id', $request->bank_id)
                ->where('user_id', $request->user_id)
                ->first();

            if (!$bank) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bank not found or does not belong to you'
                ], 404);
            }
        }

        $card->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Card updated successfully',
            'data' => new CardResource($card->load('bank'))
        ]);
    }

    /**
     * Remove the specified card.
     */
    public function destroy(Request $request, $id)
    {
        $userBankIds = Bank::where('user_id', $request->user_id)->pluck('id');
        
        $card = Card::whereIn('bank_id', $userBankIds)->findOrFail($id);
        $card->delete();

        return response()->json([
            'success' => true,
            'message' => 'Card deleted successfully'
        ]);
    }
}

