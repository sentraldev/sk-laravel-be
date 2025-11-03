<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove duplicates: keep the newest (highest id) per product_id
        DB::statement(<<<SQL
            DELETE FROM product_discounts a
            USING product_discounts b
            WHERE a.product_id = b.product_id
              AND a.id < b.id;
        SQL);

        Schema::table('product_discounts', function (Blueprint $table) {
            $table->unique('product_id');
        });
    }

    public function down(): void
    {
        Schema::table('product_discounts', function (Blueprint $table) {
            $table->dropUnique(['product_id']);
        });
    }
};
