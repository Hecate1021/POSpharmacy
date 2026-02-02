<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyRegister extends Model
{

use HasFactory;

    protected $guarded = [];
    protected $fillable = [
        'user_id',
        'opening_amount',
        'closing_amount',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
