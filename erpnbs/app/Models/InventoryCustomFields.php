<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryCustomFields extends Model
{
    use HasFactory;

    protected $fillable = [
        'field',
        'warhouses',
    ];

    /**
     * Get predefined options for fields.
     *
     * @return array
     */
    public static function getFieldOptions()
    {
        return [
            1 => 'inv ( input )',
            2 => 'WH-Rack ( input )',



        ];
    }

    /**
     * Get predefined options for warehouses.
     *
     * @return array
     */
    public static function getWarehouseOptions()
    {
        return [
            1 => 'TESTWAREHOUSE',
            2 => '3RA BRIGADA',
            3 => 'T&P Warchousc',
            4 => 'abc',
            5 => 'JPR MAIN',
            6 => 'TTT Chi Nhánh Hóc Mon',
            7 => 'DEMO WAH',
            8 => 'ML FULL',
            9 => 'ML FULL', // Duplicate entry, consider removing one.
            10 => 'Old Kampala',
            11 => 'Coche 2',
            12 => 'BBM',
            13 => 'SHOTT ADOU',
            14 => 'Gudang Spartan',
            15 => 'Left Wing',
            16 => 'TTT Chí Nhánh Củ Chi',
            17 => 'SHOTT Male',
            18 => 'De Gudang',
            19 => '12',
            20 => 'asdadadasdadadadadada',
            21 => 'Андрей',
            22 => '040',
            23 => 'Ank-Ist',
            24 => 'Desirac Hart',
            25 => 'Keya Cosmetics',
            26 => '109 East Road, Harare Zimbabwe',
        ];
    }


}
