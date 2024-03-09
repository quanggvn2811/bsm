<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'order_id',
        'quantity',
        'cost_item',
        'price_item',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
