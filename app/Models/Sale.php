<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Sale extends Model
{
    protected $guarded = [];

    // Relationship
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Isolate Sales Data
    protected static function booted()
    {
        static::addGlobalScope('branch_sales', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->branch_id) {
                    // Only show sales created by users in this branch
                    $builder->whereHas('user', function($q) use ($user) {
                        $q->where('branch_id', $user->branch_id);
                    });
                }
            }
        });
    }

    public function user() { return $this->belongsTo(User::class); }
    public function items() { return $this->hasMany(SaleItem::class); }


}
