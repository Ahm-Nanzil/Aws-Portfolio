<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryProductCategoriesSub extends Model
{
    use HasFactory;
    protected $fillable = [
        'main_category_id',
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

}
