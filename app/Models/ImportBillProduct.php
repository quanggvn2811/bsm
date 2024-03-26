<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportBillProduct extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'import_bill_id',
        'quantity',
        'price_item',
    ];
}
