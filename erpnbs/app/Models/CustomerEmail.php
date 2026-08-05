<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'to',
        'subject',
        'description',
    ];
}
