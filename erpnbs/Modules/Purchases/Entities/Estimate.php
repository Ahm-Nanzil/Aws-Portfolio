<?php

namespace Modules\Purchases\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Estimate extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'purchase_request_id',
        'estimate_number',
        'buyer_id',
        'currency',
        'estimate_date',
        'expiry_date',
        'discount_type',
        'subtotal',
        'total_discount',
        'shipping_fee',
        'grand_total',
        'vendor_note',
        'terms_conditions',
        'created_by',
    ];

    public function items()
    {
        return $this->hasMany(EstimateItems::class);
    }

    protected static function newFactory()
    {
        return \Modules\Purchases\Database\factories\EstimateFactory::new();
    }
}
