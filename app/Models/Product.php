<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
protected $fillable = [
        'branch_id',
        'barcode',              // <--- Ensure this is here
        'name',
        'category',             // <--- Ensure this is here
        'price',
        'quantity',
        'low_stock_threshold',  // <--- Ensure this is here
    ];
    // 1. The Magic Filter
    protected static function booted()
    {
        static::addGlobalScope('branch', function (Builder $builder) {
            // Check if user is logged in
            if (Auth::check()) {
                $user = Auth::user();

                // If user belongs to a specific branch (Cashier/Branch Admin)
                if ($user->branch_id) {
                    $builder->where('branch_id', $user->branch_id);
                }
                // If user is Super Admin (branch_id is NULL), they see EVERYTHING
                // No filter applied for Super Admin
            }
        });

        // 2. Auto-assign Branch on Create
        static::creating(function ($product) {
            if (Auth::check() && Auth::user()->branch_id) {
                $product->branch_id = Auth::user()->branch_id;
            }
        });
    }

  public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
