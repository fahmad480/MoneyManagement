<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::where('user_id', auth()->id());
        
        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Pagination
        $perPage = $request->get('per_page', 20);
        $categories = $query->paginate($perPage)->appends($request->all());
        
        // Check if there are default categories available to import
        $defaultCategoriesCount = Category::default()->count();
        
        return view('categories.index', compact('categories', 'defaultCategoriesCount'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'icon' => 'nullable|string|max:10',
                'color' => 'nullable|string|max:7',
                'type' => 'required|in:income,expense,both',
                'description' => 'nullable|string',
            ]);

            $validated['user_id'] = auth()->id();
            Category::create($validated);

            return redirect()->route('categories.index')
                ->with('success', 'Kategori berhasil ditambahkan!');
                
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $category = Category::where('user_id', auth()->id())->findOrFail($id);
            return view('categories.edit', compact('category'));
        } catch (Exception $e) {
            return redirect()->route('categories.index')->with('error', 'Kategori tidak ditemukan!');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $category = Category::where('user_id', auth()->id())->findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'icon' => 'nullable|string|max:10',
                'color' => 'nullable|string|max:7',
                'type' => 'required|in:income,expense,both',
                'description' => 'nullable|string',
            ]);

            $category->update($validated);

            return redirect()->route('categories.index')
                ->with('success', 'Kategori berhasil diupdate!');
                
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::where('user_id', auth()->id())->findOrFail($id);
            $category->delete();

            return redirect()->route('categories.index')
                ->with('success', 'Kategori berhasil dihapus!');
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function getDefaultCategories()
    {
        try {
            $defaultCategories = Category::default()
                ->orderBy('type')
                ->orderBy('name')
                ->get();
            
            return view('categories.import-default', compact('defaultCategories'));
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function importDefaultCategories(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_ids' => 'required|array',
                'category_ids.*' => 'exists:categories,id',
            ]);

            $userId = auth()->id();
            $imported = 0;
            
            // Get default categories to import
            $defaultCategories = Category::default()
                ->whereIn('id', $validated['category_ids'])
                ->get();
            
            foreach ($defaultCategories as $category) {
                // Check if user already has a category with the same name and type
                $exists = Category::where('user_id', $userId)
                    ->where('name', $category->name)
                    ->where('type', $category->type)
                    ->exists();
                
                if (!$exists) {
                    Category::create([
                        'user_id' => $userId,
                        'name' => $category->name,
                        'icon' => $category->icon,
                        'color' => $category->color,
                        'type' => $category->type,
                        'description' => $category->description,
                        'is_active' => $category->is_active,
                    ]);
                    $imported++;
                }
            }

            if ($imported > 0) {
                return redirect()->route('categories.index')
                    ->with('success', "$imported kategori berhasil diimport!");
            } else {
                return redirect()->route('categories.index')
                    ->with('info', 'Kategori yang dipilih sudah ada di akun Anda.');
            }
                
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
