<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealMeeting extends Model
{
    use HasFactory;
    protected $fillable = [

        'members',
        'title',
        'date',
        'time',
        'note',
        'created_by',
        'deal_id'
    ];
}
