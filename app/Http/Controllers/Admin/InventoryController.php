<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    // STEP 1: Show the Grid of Branches
    public function index()
    {
        // If not admin (e.g. Branch Manager), redirect immediately to their own inventory
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('inventory.branch', Auth::user()->branch_id);
        }

       $branches = Branch::withCount('products')->get();

    // Ensure this matches the file name above!
    return view('admin.inventory.index', compact('branches'));
    }

    // STEP 2: Show the Inventory for ONE specific branch
    public function showBranch(Branch $branch, Request $request)
{
    if (Auth::user()->role !== 'admin' && Auth::user()->branch_id !== $branch->id) {
        abort(403);
    }

    $query = $branch->products();

    if ($request->search) {
        $query->where(function($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('barcode', 'like', '%' . $request->search . '%');
        });
    }

    // UPDATED: Order by Name (A-Z) and show 100 items per page
    $products = $query->orderBy('name', 'asc')
                      ->paginate(100)
                      ->withQueryString();

    return view('admin.inventory.manage', compact('branch', 'products'));
}

    // STEP 3: Store Item (Branch ID is passed via hidden input)
    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'barcode'   => 'nullable|string|max:100|unique:products,barcode', // NEW
            'name'      => 'required|string|max:255',
            'category'  => 'required|string',
            'price'     => 'required|numeric|min:0',
            'quantity'  => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:1',
        ]);

        Product::create($request->all());

        return back()->with('success', 'Item added successfully!');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            // Ignore current product ID for unique check
            'barcode'   => 'nullable|string|max:100|unique:products,barcode,' . $product->id,
            'name'      => 'required|string|max:255',
            'category'  => 'required|string',
            'price'     => 'required|numeric|min:0',
            'quantity'  => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:1',
        ]);

        $product->update($request->all());
        return back()->with('success', 'Product updated!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Item removed.');
    }

    // Add this to your InventoryController class

    public function quickAdd(Request $request, $id)
{
    // 1. Validate that the input is a positive number
    $request->validate([
        'quantity_to_add' => 'required|integer|min:1'
    ]);

    // 2. Find the product by ID
    $product = Product::findOrFail($id);

    // 3. Perform the math: Current Database Stock + User Input
    $product->quantity = $product->quantity + $request->quantity_to_add;

    // 4. CRITICAL: Save the change to the database
    $product->save();

    // 5. Return to the page with a success message for your Toastr
    return redirect()->back()->with('success', "Added {$request->quantity_to_add} units to {$product->name}. New total: {$product->quantity}");
}
}
