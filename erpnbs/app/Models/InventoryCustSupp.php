<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryCustSupp extends Model
{
    use HasFactory;
    protected $fillable = [
        'related_type_id',
        'related_data_id',
        'order_number',
        'order_date',
        'customer_id',
        'vendor_id',
        'date_created',
        'return_type_id',
        'email',
        'phone_number',
        'order_return_number',
        'reason',
        'admin_note',
        'subtotal',
        'additional_discount',
        'total_discount',
        'total_payment',
        'items',
        'created_by',
    ];
}
