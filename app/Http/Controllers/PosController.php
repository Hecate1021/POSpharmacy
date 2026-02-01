<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function index()
    {
        // Only load available products to keep frontend light
        $products = Product::where('quantity', '>', 0)->select('id', 'name', 'price', 'quantity')->get();
        return view('pos.terminal', compact('products'));
    }

    public function store(Request $request)
    {
        $cart = $request->input('cart');

        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Cart is empty']);
        }

        // Use Database Transaction to ensure data integrity
        DB::beginTransaction();

        try {
            // 1. Calculate Total (Don't trust frontend total)
            $totalAmount = 0;
            foreach ($cart as $item) {
                $totalAmount += ($item['price'] * $item['qty']);
            }

            // 2. Create Sale Record
            $sale = Sale::create([
                'invoice_no' => 'INV-' . time(), // Simple Unique ID
                'user_id' => Auth::id(),
                'total_amount' => $totalAmount,
                'amount_paid' => $totalAmount, // Assuming exact cash for now
                'change' => 0,
                'payment_method' => 'Cash'
            ]);

            // 3. Process Items & Deduct Stock
            foreach ($cart as $item) {
                $product = Product::lockForUpdate()->find($item['id']); // Lock row to prevent race conditions

                if (!$product || $product->quantity < $item['qty']) {
                    throw new \Exception("Stock error for item: " . $item['name']);
                }

                // Deduct Stock
                $product->quantity -= $item['qty'];
                $product->save();

                // Save Sale Item
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['qty']
                ]);
            }

            DB::commit();
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // In App\Http\Controllers\PosController.php

public function getHistory()
{
    $sales = Sale::where('user_id', auth()->id())
        ->with(['items.product']) // Load items for reprint details
        ->latest()
        ->take(50) // Limit to last 50 transactions
        ->get();

    return response()->json($sales);
}
}
