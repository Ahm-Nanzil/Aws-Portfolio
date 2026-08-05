<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_code',
        'warehouse_code',
        'warehouse_name',
        'voucher_date',
        'opening_stock',
        'closing_stock',
        'lot_number_quantity_sold',
        'expiry_date',
        'serial_number',
        'note',
        'status',
    ];
}
