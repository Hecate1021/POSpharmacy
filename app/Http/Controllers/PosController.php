<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\DailyRegister; // <--- THIS IS LIKELY MISSING
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function index()
    {
        // Check if the logged-in user has already opened the register TODAY
        $register = DailyRegister::where('user_id', Auth::id())
            ->whereDate('created_at', date('Y-m-d'))
            ->first();

        $products = Product::where('quantity', '>', 0)
        ->select('id', 'name', 'price', 'quantity', 'barcode') // ADDED 'barcode'
        ->get();

    return view('pos.terminal', [
        'products' => $products,
        'register_open' => $register ? true : false,
        'starting_cash' => $register ? $register->opening_amount : 0
    ]);
    }

    // New method to handle Opening the Register
    public function openRegister(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:0']);

        DailyRegister::create([
            'user_id' => Auth::id(),
            'opening_amount' => $request->amount,
            'status' => 'open',
            'created_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function getHistory(Request $request)
    {
       $date = $request->input('date', date('Y-m-d'));
    $userId = Auth::id();

        // 1. Get Sales for the selected date
        $sales = Sale::where('user_id', $userId)
            ->whereDate('created_at', $date)
            ->with('items.product')
            ->latest()
            ->get();

        // 2. Get Starting Cash for the selected date
        $register = DailyRegister::where('user_id', $userId)
            ->whereDate('created_at', $date)
            ->first();

        $startingCash = $register ? $register->opening_amount : 0;

        // 3. Calculate Totals
        $totalSales = $sales->sum('total_amount');
        $cashOnHand = $startingCash + $totalSales;

       return response()->json([
        'debug_user_id' => $userId, // Add this to see who is logged in
        'transactions' => $sales,
        'summary' => [
            'starting_cash' => (float)$startingCash,
            'total_sales' => (float)$totalSales,
            'cash_on_hand' => (float)$cashOnHand
        ]
    ]);
    }

    public function store(Request $request)
    {
        $cart = $request->input('cart');

        // Get discount from frontend (0.10, 0.20, etc). Default to 0.
        $discountRate = $request->input('discount_rate', 0);

        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Cart is empty']);
        }

        DB::beginTransaction();

        try {
            // 1. Calculate Subtotal (Sum of Price * Qty)
            $subTotal = 0;
            foreach ($cart as $item) {
                $subTotal += ($item['price'] * $item['qty']);
            }

            // 2. Calculate Discount Amount
            $discountAmount = $subTotal * $discountRate;

            // 3. Final Total
            $finalTotal = $subTotal - $discountAmount;

            // 4. Create Sale Record
            $sale = Sale::create([
                'invoice_no' => 'INV-' . time(),
                'user_id' => Auth::id(),
                'subtotal' => $subTotal,          // New Column
                'discount_rate' => $discountRate, // New Column
                'discount_amount' => $discountAmount, // New Column
                'total_amount' => $finalTotal,
                'amount_paid' => $request->input('cash_received', $finalTotal),
                'change' => $request->input('change_amount', 0),
                'payment_method' => 'Cash'
            ]);

            // 5. Process Items & Deduct Stock
            foreach ($cart as $item) {
                $product = Product::lockForUpdate()->find($item['id']);

                if (!$product || $product->quantity < $item['qty']) {
                    throw new \Exception("Stock error for item: " . $item['name']);
                }

                $product->quantity -= $item['qty'];
                $product->save();

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['qty']
                ]);
            }

            DB::commit();

            // Reload relationships for the receipt response
            $sale->load('items.product');

            return response()->json(['success' => true, 'sale' => $sale]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
