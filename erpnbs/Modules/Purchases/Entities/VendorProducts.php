<?php

namespace Modules\Purchases\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorProducts extends Model
{
    use HasFactory;

    // Define the fillable fields
    protected $fillable = [
        'vendor',
        'categories',
        'products',
        'created_by',
        'datecreate'
    ];


    public function getVendorName()
    {
        return $this->belongsTo(\App\Models\Vender::class, 'vendor');
    }
    public function getCategoryName()
    {
        return $this->belongsTo(\App\Models\InventoryProductCategoriesMain::class, 'categories');
    }
    public function getCreatedByName()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
    public function getProductNames()
{
    $productIds = json_decode($this->products, true); // Decode JSON into an array

    if (empty($productIds)) {
        return [];
    }

    return \App\Models\ProductService::whereIn('id', $productIds)->pluck('name')->toArray();
}


    protected static function newFactory()
    {
        return \Modules\Purchases\Database\factories\VendorProductsFactory::new();
    }
}
