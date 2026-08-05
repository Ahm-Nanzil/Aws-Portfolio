<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryStockExport extends Model
{
    use HasFactory;
    protected $fillable = [
        'document_number',
        'accounting_date',
        'voucher_date',
        'invoice_id',
        'customer_id',
        'receiver',
        'address',
        'project_id',
        'type_id',
        'department_id',
        'requester_id',
        'sales_person_id',
        'invoice_no',
        'items',
        'summary_subtotal',
        'summary_discount',
        'summary_shipping_fee',
        'summary_total_payment',
        'notes',
        'created_by',
    ];
    protected $casts = [
        'items' => 'array',
        'accounting_date' => 'date',
        'voucher_date' => 'date',
        'summary_subtotal' => 'decimal:2',
        'summary_discount' => 'decimal:2',
        'summary_shipping_fee' => 'decimal:2',
        'summary_total_payment' => 'decimal:2',
    ];
}
