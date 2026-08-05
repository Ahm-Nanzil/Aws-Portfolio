<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealEmailTemplate extends Model
{
    use HasFactory;

    // Specify the table name if it doesn't follow Laravel's naming conventions
    protected $table = 'deal_email_templates';

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'to',
        'subject',
        'description',
        'page_name',
    ];


}
