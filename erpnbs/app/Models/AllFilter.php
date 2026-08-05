<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllFilter extends Model
{
    use HasFactory;
    protected $fillable = [
        'saved_by',
        'pipeline_id',
        'page_name',
        'title',
        'criteria',
    ];
}
