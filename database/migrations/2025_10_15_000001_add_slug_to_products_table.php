<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add as nullable first to allow backfill on non-empty tables
            if (!Schema::hasColumn('products', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('name');
            }
        });

        // Backfill existing rows
        DB::table('products')->orderBy('id')->chunkById(500, function ($rows) {
            foreach ($rows as $row) {
                $slug = Str::slug(($row->name ?? 'product') . '-' . ($row->sku ?? $row->id));
                DB::table('products')->where('id', $row->id)->update(['slug' => $slug]);
            }
        });

        // Note: Making column NOT NULL afterwards requires doctrine/dbal; skipping to avoid dependency.
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'slug')) {
                $table->dropUnique(['slug']);
                $table->dropColumn('slug');
            }
        });
    }
};
