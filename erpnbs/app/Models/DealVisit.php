<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealVisit extends Model
{
    use HasFactory;
    protected $fillable = [
        'deal_id',
        'title',
        'description',
        'assigned_users',
        'date',
        'time',
        'location',
        'status',
        'recurrence',
        'repeat_interval',
        'end_recurrence',
        'reminder',
        'files',
        'recurrence_status',
        'created_by'
    ];


    public static $recurrances = [
        1 => 'Daily',
        2 => 'Weekly',
        3 => 'Monthly',
        4 => 'Yearly'
    ];
    public static $status = [
        0 => 'On Going',
        1 => 'Completed'
    ];
}
