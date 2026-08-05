<?php

namespace Modules\Purchases\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseItems extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'item_id',
        'name',
        'price',
        'quantity',
        'subtotal',
        'tax',
        'tax_value',
        'total',
        'created_by',
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    protected static function newFactory()
    {
        return \Modules\Purchases\Database\factories\PurchaseItemsFactory::new();
    }
}
