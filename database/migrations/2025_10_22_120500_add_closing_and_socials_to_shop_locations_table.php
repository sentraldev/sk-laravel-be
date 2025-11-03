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
            $table->string('closing_hours')->nullable()->after('opening_hours');
            $table->string('instagram_link')->nullable()->after('closing_hours');
            $table->string('tiktok_link')->nullable()->after('instagram_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_locations', function (Blueprint $table) {
            $table->dropColumn(['closing_hours', 'instagram_link', 'tiktok_link']);
        });
    }
};
