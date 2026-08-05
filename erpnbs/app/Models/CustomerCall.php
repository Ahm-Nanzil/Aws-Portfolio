<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCall extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'subject',
        'call_type',
        'duration',
        'assigned_users',
        'description',
        'call_result',
    ];
}
