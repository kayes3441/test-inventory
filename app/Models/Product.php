<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'purchase_price',
        'sell_price',
        'opening_stock',
        'current_stock',
        'description',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'opening_stock' => 'integer',
        'current_stock' => 'integer',
    ];

}
