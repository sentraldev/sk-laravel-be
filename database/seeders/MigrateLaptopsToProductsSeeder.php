<?php

namespace Database\Seeders;

use App\Models\Laptop;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrateLaptopsToProductsSeeder extends Seeder
{
    public function run(): void
    {
        // Chunk through laptops to avoid memory issues
        Laptop::query()->orderBy('id')->chunkById(500, function ($laptops) {
            DB::transaction(function () use ($laptops) {
                foreach ($laptops as $lap) {
                    // Find target product: prefer relation, else by name/sku if applicable
                    $product = $lap->product;

                    if (! $product) {
                        // Try match by name
                        $product = Product::query()->where('name', $lap->name)->first();
                    }

                    if (! $product) {
                        // If no product found, create a minimal one
                        $product = Product::create([
                            'brand_id' => null,
                            'category_id' => null,
                            'sub_category_id' => null,
                            'sku' => 'LAP-' . $lap->id,
                            'name' => $lap->name,
                            'description' => null,
                            'price' => 0,
                            'discounted_price' => null,
                            'stock' => 0,
                            'images' => [],
                            'is_active' => true,
                        ]);
                    }

                    // Build details payload
                    $details = $product->details ?? [];
                    $merge = [
                        'processor' => $lap->processor,
                        'gpu' => $lap->gpu,
                        'ram_size' => $lap->ram_size,
                        'storage_size' => $lap->storage_size,
                    ];

                    // Include specs if present
                    if (isset($lap->specs)) {
                        $merge['specs'] = $lap->specs;
                    }

                    $details = array_filter(array_replace($details, $merge), function ($v) {
                        return $v !== null && $v !== '';
                    });

                    // Update product
                    $product->fill([
                        'discounted_price' => $lap->discounted_price ?? $product->discounted_price,
                    ]);
                    $product->details = $details;
                    $product->save();
                }
            });
        });
    }
}
