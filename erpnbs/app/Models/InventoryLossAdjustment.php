<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLossAdjustment extends Model
{
    use HasFactory;
    protected $fillable = [
        'time',
        'type',
        'warehouse',
        'item',
        'lot_number',
        'expiration_date',
        'quantity_available',
        'quantity_in_stock',
        'reason',
        'created_by',
    ];

    public function createdBy()
{
    return $this->belongsTo(User::class, 'created_by');
}



}
