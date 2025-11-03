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
        Schema::table('promos', function (Blueprint $table) {
            $table->string('title')->after('id');
            $table->string('image')->nullable()->after('title');
            $table->text('content')->nullable()->after('image');
            $table->boolean('has_voucher')->default(false)->after('active');
            $table->unsignedInteger('voucher_count')->default(0)->after('has_voucher');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->dropColumn(['title', 'image', 'content', 'has_voucher', 'voucher_count']);
        });
    }
};
