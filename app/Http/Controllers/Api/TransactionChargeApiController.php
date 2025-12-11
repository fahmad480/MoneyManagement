<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionChargeResource;
use App\Models\TransactionCharge;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionChargeApiController extends Controller
{
    /**
     * Display a listing of transaction charges.
     */
    public function index(Request $request, $transactionId)
    {
        // Verify transaction belongs to user
        $transaction = Transaction::where('user_id', $request->user_id)
            ->findOrFail($transactionId);

        $charges = TransactionCharge::where('transaction_id', $transactionId)->get();

        return response()->json([
            'success' => true,
            'message' => 'Transaction charges retrieved successfully',
            'data' => TransactionChargeResource::collection($charges)
        ]);
    }

    /**
     * Store a newly created transaction charge.
     */
    public function store(Request $request, $transactionId)
    {
        // Verify transaction belongs to user
        $transaction = Transaction::where('user_id', $request->user_id)
            ->findOrFail($transactionId);

        $validator = Validator::make($request->all(), [
            'charge_type' => 'required|in:admin_fee,tax,service_charge,other',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $charge = TransactionCharge::create(array_merge(
            $validator->validated(),
            ['transaction_id' => $transactionId]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Transaction charge created successfully',
            'data' => new TransactionChargeResource($charge)
        ], 201);
    }

    /**
     * Display the specified transaction charge.
     */
    public function show(Request $request, $transactionId, $id)
    {
        // Verify transaction belongs to user
        $transaction = Transaction::where('user_id', $request->user_id)
            ->findOrFail($transactionId);

        $charge = TransactionCharge::where('transaction_id', $transactionId)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Transaction charge retrieved successfully',
            'data' => new TransactionChargeResource($charge)
        ]);
    }

    /**
     * Update the specified transaction charge.
     */
    public function update(Request $request, $transactionId, $id)
    {
        // Verify transaction belongs to user
        $transaction = Transaction::where('user_id', $request->user_id)
            ->findOrFail($transactionId);

        $charge = TransactionCharge::where('transaction_id', $transactionId)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'charge_type' => 'in:admin_fee,tax,service_charge,other',
            'amount' => 'numeric|min:0',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $charge->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Transaction charge updated successfully',
            'data' => new TransactionChargeResource($charge)
        ]);
    }

    /**
     * Remove the specified transaction charge.
     */
    public function destroy(Request $request, $transactionId, $id)
    {
        // Verify transaction belongs to user
        $transaction = Transaction::where('user_id', $request->user_id)
            ->findOrFail($transactionId);

        $charge = TransactionCharge::where('transaction_id', $transactionId)
            ->findOrFail($id);

        $charge->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaction charge deleted successfully'
        ]);
    }
}

