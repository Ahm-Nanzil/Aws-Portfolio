<?php

namespace Modules\Purchases\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_code',
        'purchase_request_name',
        'project_id',
        'sale_estimate_id',
        'type_id',
        'currency',
        'department_id',
        'sale_invoice_id',
        'requester_id',
        'vendor_id',
        'description',
        'created_by',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseRequestItem::class);
    }

    protected static function newFactory()
    {
        return \Modules\Purchases\Database\factories\PurchaseRequestFactory::new();
    }
}
