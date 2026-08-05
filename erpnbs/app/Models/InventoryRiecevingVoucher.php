<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryRiecevingVoucher extends Model
{
    use HasFactory;
    protected $fillable = [
        'delivery_docket_number',
        'accounting_date',
        'voucher_date',
        'purchase_order',
        'supplier_name',
        'buyer',
        'project',
        'type',
        'department',
        'requester',
        'deliverer',
        'warehouse_name',
        'expiry_date',
        'invoice_no',
        'item_description',
        'item_warehouse_name',
        'quantity',
        'unit_price',
        'tax',
        'lot_number',
        'date_manufacture',
        'item_expiry_date',
        'amount',
        'total_goods_value',
        'value_of_inventory',
        'total_tax_amount',
        'total_payment',
        'notes',
    ];
}
