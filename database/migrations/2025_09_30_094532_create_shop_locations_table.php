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
        Schema::create('shop_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->decimal('lat', 10, 6)->nullable();
            $table->decimal('lng', 10, 6)->nullable();
            $table->json('opening_hours')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_locations');
    }
};
