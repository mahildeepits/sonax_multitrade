<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'month',
        'status',
        'screenshot',
        'paid_at',
        'approved_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getScreenshotUrlAttribute()
    {
        return $this->screenshot ? asset('storage/' . $this->screenshot) : null;
    }
}
