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
            // NEW: Search both Name AND Barcode
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', 'like', '%' . $request->search . '%');
            });
        }

        // Keep pagination
        $products = $query->latest()->paginate(10)->withQueryString(); // withQueryString keeps the search in the URL pages

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

    public function quickAdd(Request $request, Product $product)
    {
        $request->validate([
            'quantity_to_add' => 'required|integer|min:1',
        ]);

        // Increment the stock
        $product->increment('quantity', $request->quantity_to_add);

        return back()->with('success', "Added {$request->quantity_to_add} units to {$product->name}. New total: {$product->quantity}");
    }
}
