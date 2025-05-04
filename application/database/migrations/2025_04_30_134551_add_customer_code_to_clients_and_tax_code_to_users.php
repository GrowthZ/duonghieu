<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        
        Schema::table('clients', function (Blueprint $table) {
            $table->string('customer_code')->nullable()->unique(); 
        });

        // Thêm cột mã số thuế vào bảng users
        Schema::table('users', function (Blueprint $table) {
            $table->string('tax_code')->nullable()->unique()->after('email'); 
        });
    }

    public function down(): void
    {
       
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('customer_code');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tax_code');
        });
    }
};

