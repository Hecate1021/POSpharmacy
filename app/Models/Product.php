<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // This tells Laravel: "It is safe to save these 3 columns"
    protected $fillable = [
        'name',
        'price',
        'quantity'
    ];
}
