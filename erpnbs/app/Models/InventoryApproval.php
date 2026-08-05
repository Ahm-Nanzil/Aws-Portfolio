<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryApproval extends Model
{
    use HasFactory;
    protected $fillable = [
        'subject',
        'related',
        'staff_actions',
    ];

    public static function getRelatedOptions()
    {
        return [
            1 => 'Inventory receiving voucher',
            2 => 'Stock export',
            3 => 'Loss & adjustment',
            4 => 'Internal delivery note',
            5 => 'Packing list',
            6 => 'Order return',
        ];

    }
    public static function getStaffOptions()
    {
        return [
            1 => 'inv ( input )',
            2 => 'WH-Rack ( input )',



        ];
    }
    public static function getActionOptions()
    {
        return [
            1 => 'Approve',
            2 => 'Sign',
        ];

    }

}
