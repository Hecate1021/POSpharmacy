<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;
    protected $guarded = [];

    // Relationship: An Item belongs to a Product (to get the name)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
