<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address'];

    // 1. Relationship to User (Manager) - You likely already have this
    public function manager()
    {
        return $this->hasOne(User::class)->where('role', 'branch_manager');
    }

    // 2. Relationship to All Users (Staff) - You might have this too
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // 3. ADD THIS MISSING FUNCTION
    public function products()
    {
        // This tells Laravel: "One Branch has MANY Products"
        return $this->hasMany(Product::class);
    }

    public function sales()
    {
        // This allows us to get all sales that belong to this branch's cashiers
        // It works by going through the User model (Branch -> hasMany Users -> hasMany Sales)
        return $this->hasManyThrough(Sale::class, User::class, 'branch_id', 'user_id');
    }
}
