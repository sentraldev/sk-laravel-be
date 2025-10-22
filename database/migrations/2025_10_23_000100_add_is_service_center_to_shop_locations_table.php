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
        Schema::table('shop_locations', function (Blueprint $table) {
            $table->boolean('is_service_center')->default(false)->after('tiktok_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_locations', function (Blueprint $table) {
            $table->dropColumn('is_service_center');
        });
    }
};
