<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionApiController extends Controller
{
    /**
     * Display a listing of transactions.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $transactions = Transaction::where('user_id', $request->user_id)
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->bank_id, function ($query, $bankId) {
                $query->where('bank_id', $bankId);
            })
            ->when($request->category_id, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($request->start_date, function ($query, $startDate) {
                $query->whereDate('transaction_date', '>=', $startDate);
            })
            ->when($request->end_date, function ($query, $endDate) {
                $query->whereDate('transaction_date', '<=', $endDate);
            })
            ->with(['bank', 'card', 'category', 'toBank', 'charges'])
            ->orderBy('transaction_date', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Transactions retrieved successfully',
            'data' => TransactionResource::collection($transactions),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ]
        ]);
    }

    /**
     * Store a newly created transaction.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_id' => 'required|exists:banks,id',
            'card_id' => 'nullable|exists:cards,id',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,debit,credit,transfer,e-wallet',
            'source' => 'nullable|string|max:255',
            'to_bank_id' => 'nullable|exists:banks,id',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'transaction_date' => 'required|date',
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

        $transaction = Transaction::create(array_merge(
            $validator->validated(),
            ['user_id' => $request->user_id]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Transaction created successfully',
            'data' => new TransactionResource($transaction->load(['bank', 'card', 'category', 'toBank', 'charges']))
        ], 201);
    }

    /**
     * Display the specified transaction.
     */
    public function show(Request $request, $id)
    {
        $transaction = Transaction::where('user_id', $request->user_id)
            ->with(['bank', 'card', 'category', 'toBank', 'charges'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Transaction retrieved successfully',
            'data' => new TransactionResource($transaction)
        ]);
    }

    /**
     * Update the specified transaction.
     */
    public function update(Request $request, $id)
    {
        $transaction = Transaction::where('user_id', $request->user_id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'bank_id' => 'exists:banks,id',
            'card_id' => 'nullable|exists:cards,id',
            'category_id' => 'exists:categories,id',
            'type' => 'in:income,expense,transfer',
            'amount' => 'numeric|min:0',
            'payment_method' => 'in:cash,debit,credit,transfer,e-wallet',
            'source' => 'nullable|string|max:255',
            'to_bank_id' => 'nullable|exists:banks,id',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'transaction_date' => 'date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $transaction->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Transaction updated successfully',
            'data' => new TransactionResource($transaction->load(['bank', 'card', 'category', 'toBank', 'charges']))
        ]);
    }

    /**
     * Remove the specified transaction.
     */
    public function destroy(Request $request, $id)
    {
        $transaction = Transaction::where('user_id', $request->user_id)
            ->findOrFail($id);

        $transaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaction deleted successfully'
        ]);
    }
}

