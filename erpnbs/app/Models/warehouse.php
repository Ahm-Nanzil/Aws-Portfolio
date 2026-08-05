<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',               // Warehouse name
        'address',            // Warehouse address
        'city',               // City
        'city_zip',           // City zip code
        'created_by',         // Created by (user ID)
        'code',               // Warehouse code
        'state',              // State
        'postal_code',        // Postal code
        'staffs',             // Assigned staff (JSON)
        'display',            // Display checkbox (default true)
        'hide_when_out_of_stock', // Hide when out of stock (default false)
        'note',               // Note
    ];

    public static function warehouse_id($warehouse_name)
    {
        $warehouse = DB::table('warehouses')
        ->where('id', $warehouse_name)
        ->where('created_by', Auth::user()->creatorId())
        ->select('id')
        ->first();

        return ($warehouse != null) ? $warehouse->id : 0;
    }
}
