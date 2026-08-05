<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryPackingList extends Model
{
    use HasFactory;

    // Defining fillable fields for the model
    protected $fillable = [
        'stock_export_id',
        'customer_id',
        'bill_to',
        'ship_to',
        'packing_list_number',
        'width',
        'height',
        'length',
        'weight',
        'volume',
        'client_note',
        'admin_note',
        'items', // Assuming the items are stored as JSON
        'subtotal',
        'additional_discount',
        'total_discount',
        'shipping_fee',
        'total_payment',
        'created_by'
    ];
}
