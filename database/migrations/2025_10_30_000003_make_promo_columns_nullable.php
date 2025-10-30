<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE promos MODIFY code VARCHAR(255) NULL");
            DB::statement("ALTER TABLE promos MODIFY `type` ENUM('percentage','fixed') NULL");
            DB::statement("ALTER TABLE promos MODIFY `value` DECIMAL(12,2) NULL");
            DB::statement("ALTER TABLE promos MODIFY has_voucher TINYINT(1) NULL DEFAULT 0");
            DB::statement("ALTER TABLE promos MODIFY voucher_count INT UNSIGNED NULL DEFAULT 0");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE promos ALTER COLUMN code DROP NOT NULL");
            DB::statement("ALTER TABLE promos ALTER COLUMN \"type\" DROP NOT NULL");
            DB::statement("ALTER TABLE promos ALTER COLUMN \"value\" DROP NOT NULL");
            DB::statement("ALTER TABLE promos ALTER COLUMN has_voucher DROP NOT NULL");
            DB::statement("ALTER TABLE promos ALTER COLUMN voucher_count DROP NOT NULL");
        } else {
            // Fallback: try Laravel's schema change() if available (requires doctrine/dbal)
            Schema::table('promos', function ($table) {
                try {
                    $table->string('code')->nullable()->change();
                    $table->enum('type', ['percentage', 'fixed'])->nullable()->change();
                    $table->decimal('value', 12, 2)->nullable()->change();
                    $table->boolean('has_voucher')->nullable()->default(false)->change();
                    $table->unsignedInteger('voucher_count')->nullable()->default(0)->change();
                } catch (Throwable $e) {
                    // No-op: manual intervention required for unsupported drivers
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE promos MODIFY code VARCHAR(255) NOT NULL");
            DB::statement("ALTER TABLE promos MODIFY `type` ENUM('percentage','fixed') NOT NULL");
            DB::statement("ALTER TABLE promos MODIFY `value` DECIMAL(12,2) NOT NULL");
            DB::statement("ALTER TABLE promos MODIFY has_voucher TINYINT(1) NOT NULL DEFAULT 0");
            DB::statement("ALTER TABLE promos MODIFY voucher_count INT UNSIGNED NOT NULL DEFAULT 0");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE promos ALTER COLUMN code SET NOT NULL");
            DB::statement("ALTER TABLE promos ALTER COLUMN \"type\" SET NOT NULL");
            DB::statement("ALTER TABLE promos ALTER COLUMN \"value\" SET NOT NULL");
            DB::statement("ALTER TABLE promos ALTER COLUMN has_voucher SET NOT NULL");
            DB::statement("ALTER TABLE promos ALTER COLUMN voucher_count SET NOT NULL");
        } else {
            Schema::table('promos', function ($table) {
                try {
                    $table->string('code')->nullable(false)->change();
                    $table->enum('type', ['percentage', 'fixed'])->nullable(false)->change();
                    $table->decimal('value', 12, 2)->nullable(false)->change();
                    $table->boolean('has_voucher')->nullable(false)->default(false)->change();
                    $table->unsignedInteger('voucher_count')->nullable(false)->default(0)->change();
                } catch (Throwable $e) {
                    // No-op
                }
            });
        }
    }
};
