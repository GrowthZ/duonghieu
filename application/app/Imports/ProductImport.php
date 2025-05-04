<?php

namespace App\Imports;



namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToModel, WithHeadingRow
{
    // Counter to track successful imports
    public $successfulImports = 0;
    public $errorImports = 0;

    public function model(array $row)
    {
        if (!isset($row['material_id']) || !$row['quantity'] || !$row['discount']) {
            $this->errorImports++; // Increment the error import count
            return;
        }
        $product = Product::where('material_id', $row['material_id'])->first();
    
        if ($product) {
            $product->quantity = (int)$row['quantity'] + (int)$product->quantity;
        } else {
            $product = new Product([
                'status' => $row['status'],
                'material_id' => $row['material_id'],
                'description' => $row['description'],
                'price_vnd' => $row['price_vnd'],
                'price_unit' => $row['price_unit'],
                'quantity' => $row['quantity'],
                'discount' => $row['discount'],
                'reg_price_group' => $row['reg_price_group'],
                'hq_price_group' => $row['hq_price_group'],
                'price_p_pos_vnd' => $row['price_p_pos_vnd'],
                'returnable' => $row['returnable'],
                'upc' => $row['upc'],
                'export_control_regulations' => $row['export_control_regulations'],
                'customer_price_vnd' => $row['customer_price_vnd'],
                'product_group' => $row['product_group'],
                'metal_factor' => $row['metal_factor'],
                'commodity_code' => $row['commodity_code'],
                'net_weight_kg' => $row['net_weight_kg'],
                'item_class' => $row['item_class'],
                'country_of_origin' => $row['country_of_origin'],
                'software_licenses' => $row['software_licenses'],
                'estimated_dispatch_time_working_days' => $row['estimated_dispatch_time_working_days'],
                'lkz_fdb_catalogid' => $row['lkz_fdb_catalogid'],
                'group_code' => $row['group_code'],
                'business_unit' => $row['business_unit'],
                'e_text' => $row['e_text'],
                'c_text' => $row['c_text'],
                'f_text' => $row['f_text'],
            ]);
            
        }

        if ($product->save()) {
            $this->successfulImports++; // Increment the successful import count
        }
        return $product;
    }

    // Optional: Method to get the number of successful imports
    public function getSuccessfulImports()
    {
        return $this->successfulImports;
    }
    public function getErrorImports()
    {
        return $this->errorImports;
    }
}
