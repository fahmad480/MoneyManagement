<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryApiController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $categories = Category::where('user_id', $request->user_id)
            ->when($request->is_active, function ($query, $isActive) {
                $query->where('is_active', $isActive);
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Categories retrieved successfully',
            'data' => CategoryResource::collection($categories),
            'meta' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
            ]
        ]);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'type' => 'required|in:income,expense',
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

        $category = Category::create(array_merge(
            $validator->validated(),
            ['user_id' => $request->user_id]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => new CategoryResource($category)
        ], 201);
    }

    /**
     * Display the specified category.
     */
    public function show(Request $request, $id)
    {
        $category = Category::where('user_id', $request->user_id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Category retrieved successfully',
            'data' => new CategoryResource($category)
        ]);
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, $id)
    {
        $category = Category::where('user_id', $request->user_id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'type' => 'in:income,expense',
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

        $category->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => new CategoryResource($category)
        ]);
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Request $request, $id)
    {
        $category = Category::where('user_id', $request->user_id)
            ->findOrFail($id);

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }
}
