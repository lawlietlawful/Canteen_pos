<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display categories listing with product count
     */
    public function index(): View
    {
        $categories = Category::withCount('products')
            ->orderBy('category_name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_name' => ['required', 'string', 'max:100', Rule::unique(Category::class, 'category_name')],
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', "Category \"{$validated['category_name']}\" added successfully.");
    }

    /**
     * Update an existing category
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'category_name' => [
                'required', 
                'string', 
                'max:100', 
                Rule::unique(Category::class, 'category_name')->ignore($category->category_id, 'category_id')
            ],
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', "Category updated to \"{$category->category_name}\".");
    }

    /**
     * Remove a category (Block delete if products exist!)
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            $count = $category->products()->count();
            return back()->with('warning', "Cannot delete category \"{$category->category_name}\" because it contains {$count} product(s). Please reassign or delete those products first.");
        }

        $name = $category->category_name;
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', "Category \"{$name}\" was deleted successfully.");
    }
}

