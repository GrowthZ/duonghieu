<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'material_id',
        'description',
        'price',
        'price_unit',
        'quantity',
        'discount',
        'reg_price_group',
        'hq_price_group',
        'price_per_pos',
        'returnable',
        'upc',
        'export_control_regulations',
        'customer_price',
        'product_group',
        'metal_factor',
        'commodity_code',
        'net_weight',
        'item_class',
        'country_of_origin',
        'software_licenses',
        'estimated_dispatch_time',
        'catalog_id',
        'group_code',
        'business_unit',
        'e_text',
        'c_text',
        'f_text',
    ];
}
