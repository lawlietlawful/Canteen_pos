<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a paginated listing of products with search and category filters
     */
    public function index(Request $request): View
    {
        $categories = Category::orderBy('category_name')->get();

        $query = Product::with('category')->withCount('orderItems');

        if ($request->filled('category')) {
            $query->where('category_id', (int)$request->category);
        }

        if ($request->filled('search')) {
            $term = '%' . trim($request->search) . '%';
            $query->where('product_name', 'ilike', $term);
        }

        $products = $query->orderBy('product_name')->paginate(15)->withQueryString();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show form to create a new product
     */
    public function create(): View
    {
        $categories = Category::orderBy('category_name')->get();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id'  => ['required', Rule::exists(Category::class, 'category_id')],
            'product_name' => 'required|string|max:150',
            'price'        => 'required|numeric|min:0',
            'stock_qty'    => 'required|integer|min:0',
            'is_available' => 'nullable|boolean',
        ]);

        $validated['is_available'] = $request->has('is_available');

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', "Product \"{$validated['product_name']}\" added successfully.");
    }

    /**
     * Show form to edit an existing product
     */
    public function edit(Product $product): View
    {
        $categories = Category::orderBy('category_name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update an existing product
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id'  => ['required', Rule::exists(Category::class, 'category_id')],
            'product_name' => 'required|string|max:150',
            'price'        => 'required|numeric|min:0',
            'stock_qty'    => 'required|integer|min:0',
            'is_available' => 'nullable|boolean',
        ]);

        $validated['is_available'] = $request->has('is_available');

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', "Product \"{$product->product_name}\" updated successfully.");
    }

    /**
     * Remove the specified product (Prevent delete if order_items exist!)
     */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->orderItems()->exists()) {
            return back()->with('warning', "Cannot delete product \"{$product->product_name}\" because it is referenced in past sales order records. You can toggle it to 'Unavailable' instead.");
        }

        $name = $product->product_name;
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', "Product \"{$name}\" has been deleted.");
    }

    /**
     * Toggle product availability status
     */
    public function toggleAvailability(Product $product): RedirectResponse
    {
        $product->update([
            'is_available' => !$product->is_available,
        ]);

        $status = $product->is_available ? 'Available' : 'Unavailable';
        return back()->with('success', "Product \"{$product->product_name}\" is now marked as {$status}.");
    }

    /**
     * Restock inventory for a product
     */
    public function restock(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'add_qty' => 'required|integer|min:1',
        ]);

        $product->increment('stock_qty', $validated['add_qty']);

        return back()->with('success', "Added +{$validated['add_qty']} units to \"{$product->product_name}\". New stock: {$product->stock_qty}.");
    }
}

