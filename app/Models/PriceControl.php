<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceControl extends Model
{
    use SoftDeletes;

    protected $fillable = ['shop_id', 'product_id', 'price', 'notes'];
}