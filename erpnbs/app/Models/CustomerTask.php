<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'name',
        'date',
        'time',
        'priority',
        'status',
        'recurrence',
        'repeat_interval',
        'end_recurrence',
        'reminder',
        'recurrence_status',
        'created_by',
        'assigned_users',
    ];

    public static $priorities = [
        1 => 'Low',
        2 => 'Medium',
        3 => 'High',
    ];
    public static $status = [
        0 => 'On Going',
        1 => 'Completed'
    ];

    public static $recurrances = [
        1 => 'Daily',
        2 => 'Weekly',
        3 => 'Monthly',
        4 => 'Yearly'
    ];

}
