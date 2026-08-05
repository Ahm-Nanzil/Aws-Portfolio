<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryDeliveryNotes extends Model
{
    use HasFactory;
    protected $fillable = [
        'internal_delivery_name',
        'internal_delivery_number',
        'accounting_date',
        'voucher_date',
        'deliverer',
        'notes',
        'item_description',
        'from_stock_name',
        'to_stock_name',
        'available_quantity',
        'quantity',
        'unit_price',
        'amount',
        'total_amount',
    ];
}
