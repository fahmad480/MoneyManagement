<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BankResource;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BankApiController extends Controller
{
    /**
     * Display a listing of banks.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $banks = Bank::where('user_id', $request->user_id ?? auth()->id())
            ->when($request->is_active, function ($query, $isActive) {
                $query->where('is_active', $isActive);
            })
            ->when($request->bank_type, function ($query, $bankType) {
                $query->where('bank_type', $bankType);
            })
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Banks retrieved successfully',
            'data' => BankResource::collection($banks),
            'meta' => [
                'current_page' => $banks->currentPage(),
                'last_page' => $banks->lastPage(),
                'per_page' => $banks->perPage(),
                'total' => $banks->total(),
            ]
        ]);
    }

    /**
     * Store a newly created bank.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => 'required|string|max:255',
            'account_nickname' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'current_balance' => 'required|numeric|min:0',
            'bank_type' => 'required|in:debit,credit,savings,current',
            'branch' => 'nullable|string|max:255',
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

        $bank = Bank::create(array_merge(
            $validator->validated(),
            ['user_id' => $request->user_id ?? auth()->id()]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Bank created successfully',
            'data' => new BankResource($bank)
        ], 201);
    }

    /**
     * Display the specified bank.
     */
    public function show(Request $request, $id)
    {
        $bank = Bank::where('user_id', $request->user_id ?? auth()->id())
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Bank retrieved successfully',
            'data' => new BankResource($bank)
        ]);
    }

    /**
     * Update the specified bank.
     */
    public function update(Request $request, $id)
    {
        $bank = Bank::where('user_id', $request->user_id ?? auth()->id())
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'bank_name' => 'string|max:255',
            'account_nickname' => 'string|max:255',
            'account_number' => 'string|max:255',
            'current_balance' => 'numeric|min:0',
            'bank_type' => 'in:debit,credit,savings,current',
            'branch' => 'nullable|string|max:255',
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

        $bank->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Bank updated successfully',
            'data' => new BankResource($bank)
        ]);
    }

    /**
     * Remove the specified bank.
     */
    public function destroy(Request $request, $id)
    {
        $bank = Bank::where('user_id', $request->user_id ?? auth()->id())
            ->findOrFail($id);

        $bank->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bank deleted successfully'
        ]);
    }
}

