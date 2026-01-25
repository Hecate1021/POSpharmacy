<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $guarded = []; // Allow mass assignment

    // Relationship: A Sale has many Items
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    // Relationship: A Sale belongs to a Cashier
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
