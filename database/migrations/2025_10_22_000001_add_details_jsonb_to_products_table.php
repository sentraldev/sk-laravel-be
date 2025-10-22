<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add details JSONB and ensure discounted_price exists (skip if present)
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'details')) {
                // Use a DB statement to guarantee JSONB type on PostgreSQL
                DB::statement("ALTER TABLE products ADD COLUMN details JSONB DEFAULT '{}'::jsonb NOT NULL");
            }

            if (!Schema::hasColumn('products', 'discounted_price')) {
                $table->decimal('discounted_price', 12, 2)->nullable()->after('price');
            }
        });

        // Create GIN index for JSONB column
        DB::statement('CREATE INDEX IF NOT EXISTS products_details_gin ON products USING GIN (details)');
    }

    public function down(): void
    {
        // Drop GIN index and column if exist
        DB::statement('DROP INDEX IF EXISTS products_details_gin');

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'details')) {
                DB::statement('ALTER TABLE products DROP COLUMN details');
            }

            // Do not drop discounted_price if it already existed before this migration
            // Only drop if it was added by this migration and still present
            if (Schema::hasColumn('products', 'discounted_price')) {
                // Safe to drop only if the older migration doesn't manage it; comment out if undesired
                // $table->dropColumn('discounted_price');
            }
        });
    }
};
