<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportBill extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'date',
        'total',
        'notes',
    ];

    public function import_bill_products()
    {
        return $this->hasMany(ImportBillProduct::class);
    }
}
