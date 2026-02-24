<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * KUK 017: Terstruktur - CRUD dengan structured programming
 * KUK 018: OOP - Resource controller pattern
 */
class CategoryController extends Controller
{
    /**
     * Display a listing of categories
     * KUK 020: SQL - Query dengan ordering
     */
    public function index()
    {
        $categories = Category::withCount('documents')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created category
     * KUK 025: Debugging - Validation dan error handling
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories',
                'description' => 'nullable|string',
                'color' => 'required|string|size:7',
            ]);

            $validated['slug'] = Str::slug($validated['name']);
            $validated['icon'] = '📁'; // Default icon

            $category = Category::create($validated);

            return redirect()
                ->route('categories.index')
                ->with('success', 'Kategori berhasil ditambahkan!');

        } catch (\Exception $e) {
            // KUK 025: Debugging - Error logging
            \Log::error('Error creating category: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan kategori: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified category
     */
    public function show(Category $category)
    {
        $category->load(['documents' => function($query) {
            $query->latest()->take(20);
        }]);

        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified category
     * KUK 047: Update system
     */
    public function update(Request $request, Category $category)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
                'description' => 'nullable|string',
                'color' => 'required|string|size:7',
            ]);

            $validated['slug'] = Str::slug($validated['name']);
            if (!isset($validated['icon'])) {
                $validated['icon'] = $category->icon ?? '📁';
            }

            $category->update($validated);

            return redirect()
                ->route('categories.index')
                ->with('success', 'Kategori berhasil diupdate!');

        } catch (\Exception $e) {
            \Log::error('Error updating category: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate kategori: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified category
     * KUK 047: Soft delete implementation
     */
    public function destroy(Category $category)
    {
        try {
            if ($category->documents()->count() > 0) {
                return redirect()
                    ->route('categories.index')
                    ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki dokumen!');
            }

            $category->delete();

            return redirect()
                ->route('categories.index')
                ->with('success', 'Kategori berhasil dihapus!');

        } catch (\Exception $e) {
            \Log::error('Error deleting category: ' . $e->getMessage());

            return redirect()
                ->route('categories.index')
                ->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }
}
