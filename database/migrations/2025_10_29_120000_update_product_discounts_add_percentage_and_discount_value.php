<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_discounts', function (Blueprint $table) {
            $table->decimal('percentage', 5, 2)->nullable()->after('value');
            $table->decimal('discount_value', 12, 2)->nullable()->after('percentage');
        });

        // Migrate existing data from (type, value) to (percentage, discount_value)
        // Postgres-friendly UPDATE with JOIN
        DB::statement(<<<SQL
            UPDATE product_discounts pd
            SET
                percentage = CASE
                    WHEN pd.type = 'percentage' THEN pd.value
                    WHEN pd.type = 'fixed' THEN CASE WHEN p.price > 0 THEN ROUND((pd.value / p.price) * 100, 2) ELSE 0 END
                    ELSE NULL
                END,
                discount_value = CASE
                    WHEN pd.type = 'percentage' THEN ROUND(p.price * (pd.value / 100), 2)
                    WHEN pd.type = 'fixed' THEN pd.value
                    ELSE NULL
                END
            FROM products p
            WHERE pd.product_id = p.id
        SQL);

        Schema::table('product_discounts', function (Blueprint $table) {
            $table->dropColumn(['type', 'value']);
        });
    }

    public function down(): void
    {
        Schema::table('product_discounts', function (Blueprint $table) {
            $table->enum('type', ['percentage', 'fixed'])->default('fixed');
            $table->decimal('value', 12, 2)->default(0);
        });

        // Best-effort reverse: set type to fixed and copy discount_value back to value
        DB::statement(<<<SQL
            UPDATE product_discounts pd
            SET
                type = 'fixed',
                value = COALESCE(pd.discount_value, 0)
        SQL);

        Schema::table('product_discounts', function (Blueprint $table) {
            $table->dropColumn(['percentage', 'discount_value']);
        });
    }
};
