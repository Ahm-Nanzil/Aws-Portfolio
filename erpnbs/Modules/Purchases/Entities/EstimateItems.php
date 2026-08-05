<?php

namespace Modules\Purchases\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EstimateItems extends Model
{
    use HasFactory;

    protected $fillable = [
        'estimate_id',
        'item_id',
        'item_name',
        'unit_price',
        'quantity',
        'subtotal_before_tax',
        'tax',
        'tax_value',
        'subtotal_after_tax',
        'discount_percentage',
        'discount_money',
        'total',
        'created_by',
    ];

    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }

    protected static function newFactory()
    {
        return \Modules\Purchases\Database\factories\EstimateItemsFactory::new();
    }
}
