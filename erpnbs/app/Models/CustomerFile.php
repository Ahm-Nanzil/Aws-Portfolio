<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerFile extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'file_name',
        'file_path',
    ];
}
