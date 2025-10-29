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
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'discounted_price')) {
                $table->dropColumn('discounted_price');
            }
            if (Schema::hasColumn('products', 'discount_value')) {
                $table->dropColumn('discount_value');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'discounted_price')) {
                $table->decimal('discounted_price', 12, 2)->nullable()->after('price');
            }
            if (! Schema::hasColumn('products', 'discount_value')) {
                $table->unsignedTinyInteger('discount_value')->nullable()->after('discounted_price');
            }
        });
    }
};
