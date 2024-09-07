<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    const DEFAULT_CATEGORY_NAME = 'DEFAULT_CATEGORY';

    protected $fillable = [
        'name',
        'description',
        'status',
        'sku',
        'stock_id',
    ];
}
