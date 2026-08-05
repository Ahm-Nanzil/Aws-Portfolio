<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryColors extends Model
{
    use HasFactory;
    protected $fillable = [
        'color_code',
        'color_name',
        'color_hex',
        'order',
        'note',
        'display',
    ];
}
