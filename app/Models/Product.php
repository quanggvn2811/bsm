<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    const PUBLIC_PROD_IMAGE_FOLDER = 'Pro_Images';

    const TYPE_SINGLE = 1;
    const TYPE_MULTIPLE = 2;
    const TYPE_OTHER = 3;

    const PRODUCT_TYPE = [
        self::TYPE_SINGLE => 'Single',
        self::TYPE_MULTIPLE => 'Multiple',
        self::TYPE_OTHER => 'Other',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'images',
        'status',
        'sku', // Todo: make sku unique
        'supplier_sku',
        'cost',
        'price',
        'category_id',
        'supplier_id',
        'quantity',
        'type',
        'sub_product_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function order_detail()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function product_supplier()
    {
        return $this->hasMany(SuppliersProduct::class);
    }
}
