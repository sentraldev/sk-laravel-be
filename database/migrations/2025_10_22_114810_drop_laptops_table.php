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
        // Drop the laptops table and all its data
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('laptops');
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the laptops table schema (best-effort based on previous migrations)
        Schema::create('laptops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('brand');
            $table->string('processor')->nullable();
            $table->string('gpu')->nullable();
            $table->unsignedSmallInteger('ram_size')->nullable(); // in GB
            $table->unsignedSmallInteger('storage_size')->nullable(); // in GB
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('discounted_price', 12, 2)->nullable();
            $table->longText('specs')->nullable();
            $table->timestamps();
        });
    }
};
