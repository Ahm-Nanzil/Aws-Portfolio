<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryStyle extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'barcode',
        'name',
        'order',
        'display',
        'note',
    ];
}
