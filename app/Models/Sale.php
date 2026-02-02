<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Support\Facades\Auth; <--- No longer needed if we remove the scope
// use Illuminate\Database\Eloquent\Builder; <--- No longer needed
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Sale extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'invoice_no',
        'user_id',
        'subtotal',
        'discount_rate',
        'discount_amount',
        'total_amount',
        'amount_paid',
        'change',
        'payment_method'
    ];

    // --- REMOVED THE 'booted' METHOD THAT CAUSED THE CRASH ---

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
