<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_name',
        'received',
        'daily_franchise',
        'retail',
        'courier_transfer',
        'pending',
        'meeting_order',
        'staff_issued',
        'director_issued',
        'return',
        'promotion',
        'sample',
        'damaged',
        'total_stocks_remaining',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
