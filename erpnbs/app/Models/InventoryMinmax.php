<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMinmax extends Model
{
    use HasFactory;
    protected $fillable = [
        'commodity_code',
        'commodity_name',
        'sku_code',
        'min_inventory_value',
        'max_inventory_qty',


    ];
}
