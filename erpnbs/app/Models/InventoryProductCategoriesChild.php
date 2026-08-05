<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryProductCategoriesChild extends Model
{
    use HasFactory;
    protected $fillable = [
        'main_category_id',
        'sub_category_id',
        'code',
        'name',
        'order',
        'display',
        'note',
    ];
    public function mainCategory()
    {
        return $this->belongsTo(InventoryProductCategoriesMain::class, 'main_category_id');
    }
    public function subCategory()
    {
        return $this->belongsTo(InventoryProductCategoriesSub::class, 'sub_category_id');
    }
}
