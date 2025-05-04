<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('status')->nullable();
            $table->string('material_id')->nullable(); 
            $table->string('description')->nullable();
            $table->unsignedBigInteger('price')->nullable(); 
            $table->string('price_unit')->nullable();
            $table->integer('quantity')->default(0);
            $table->float('discount')->nullable(); 
            $table->string('reg_price_group')->nullable();
            $table->string('hq_price_group')->nullable();
            $table->unsignedBigInteger('price_per_pos')->nullable();
            $table->boolean('returnable')->default(false);
            $table->string('upc')->nullable();
            $table->string('export_control_regulations')->nullable();
            $table->unsignedBigInteger('customer_price')->nullable();
            $table->string('product_group')->nullable();
            $table->float('metal_factor')->nullable();
            $table->string('commodity_code')->nullable();
            $table->float('net_weight')->nullable();
            $table->string('item_class')->nullable();
            $table->string('country_of_origin')->nullable();
            $table->boolean('software_licenses')->default(false);
            $table->integer('estimated_dispatch_time')->nullable(); 
            $table->string('catalog_id')->nullable(); 
            $table->string('group_code')->nullable();
            $table->string('business_unit')->nullable();
            $table->text('e_text')->nullable();
            $table->text('c_text')->nullable();
            $table->text('f_text')->nullable();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
