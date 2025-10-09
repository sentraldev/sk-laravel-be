<?php

namespace App\Services\Imports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Laptop;
use App\Models\Product;
use App\Models\ProductDiscount;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;

class LaptopRowsImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            return;
        }

        $first = $rows->first();
        $header = collect($first)->map(function ($v) {
            return is_string($v) ? strtolower(trim($v)) : $v;
        })->toArray();

        // Expected headers: item_no, name, brand, processor, gpu, ram, ssd, specs, price, discounted_price
        $hasHeader = is_array($header) && in_array('name', $header, true) && in_array('brand', $header, true);

        if ($hasHeader) {
            $rows = $rows->slice(1);
            foreach ($rows as $row) {
                $assoc = array_combine($header, $row->toArray());
                if (! $assoc) { continue; }
                $data = [
                    'item_no' => $assoc['item_no'] ?? null,
                    'name' => $assoc['name'] ?? null,
                    'brand' => $assoc['brand'] ?? null,
                    'processor' => $assoc['processor'] ?? null,
                    'gpu' => $assoc['gpu'] ?? null,
                    'ram_size' => $this->normalizeInt($assoc['ram'] ?? null),
                    'storage_size' => $this->normalizeStorageSize($assoc['ssd'] ?? null),
                    'specs' => $assoc['specs'] ?? null,
                    'price' => $this->normalizeMoney($assoc['price'] ?? null),
                    'discounted_price' => $this->normalizeMoney($assoc['discounted_price'] ?? null),
                ];
                $this->storeRow($data);
            }
        } else {
            // Fallback to positional mapping
            foreach ($rows as $row) {
                $arr = $row->toArray();
                $data = [
                    'item_no' => Arr::get($arr, 0),
                    'name' => Arr::get($arr, 1),
                    'brand' => Arr::get($arr, 2),
                    'processor' => Arr::get($arr, 3),
                    'gpu' => Arr::get($arr, 4),
                    'ram_size' => $this->normalizeInt(Arr::get($arr, 5)),
                    'storage_size' => $this->normalizeStorageSize(Arr::get($arr, 6)),
                    'specs' => Arr::get($arr, 7),
                    'price' => $this->normalizeMoney(Arr::get($arr, 8)),
                    'discounted_price' => $this->normalizeMoney(Arr::get($arr, 9)),
                ];
                $this->storeRow($data);
            }
        }
    }

    protected function storeRow(array $data): void
    {
        $name = trim((string) ($data['name'] ?? ''));
        $brandName = trim((string) ($data['brand'] ?? ''));
        if ($name === '' || $brandName === '') {
            return;
        }

        $price = $data['price'] ?? null;
        $discounted = $data['discounted_price'] ?? null;
        if (is_numeric($price) && is_numeric($discounted) && $discounted > $price) {
            // Swap if discounted price is higher than price
            [$price, $discounted] = [$discounted, $price];
        }

        // Ensure base refs
        $brand = Brand::firstOrCreate(
            ['name' => $brandName],
            ['slug' => Str::slug($brandName)]
        );
        $category = Category::firstOrCreate(
            ['name' => 'Laptop'],
            ['slug' => Str::slug('Laptop')]
        );

        // Create or update Product by SKU if present, else by name+brand
        $sku = $this->normalizeSku($data['item_no'] ?? null) ?? Str::slug($name);
        $product = Product::firstOrNew(['sku' => $sku]);
        $product->brand_id = $brand->id;
        $product->category_id = $category->id;
        $product->name = $name;
        // Use specs as description if provided
        if (! empty($data['specs'])) {
            $product->description = is_string($data['specs']) ? $data['specs'] : json_encode($data['specs']);
        }
        if (is_numeric($price)) {
            $product->price = $price;
        }
        if (is_numeric($discounted)) {
            $product->discounted_price = $discounted;
        }
        $product->stock = $product->stock ?? 0;
        $product->is_active = $product->is_active ?? true;
        $product->save();

        // Optionally create a ProductDiscount record if a discount is present and valid
        if (is_numeric($price) && is_numeric($discounted) && $discounted < $price) {
            ProductDiscount::create([
                'product_id' => $product->id,
                'type' => 'fixed',
                'value' => $price - $discounted,
                'starts_at' => null,
                'ends_at' => null,
                'active' => true,
            ]);
        }

        // Create or update Laptop and link to Product (also persist specs)
        Laptop::updateOrCreate(
            [
                'name' => $name,
                'brand' => $brandName,
            ],
            [
                'processor' => $data['processor'] ?? null,
                'gpu' => $data['gpu'] ?? null,
                'ram_size' => is_numeric($data['ram_size'] ?? null) ? (int) $data['ram_size'] : null,
                'storage_size' => is_numeric($data['storage_size'] ?? null) ? (int) $data['storage_size'] : null,
                'specs' => isset($data['specs']) && $data['specs'] !== '' ? (string) $data['specs'] : null,
                'product_id' => $product->id,
            ]
        );
    }

    private function normalizeInt($value): ?int
    {
        if ($value === null) return null;
        if (is_string($value)) {
            // Handle patterns like "128+256"
            if (preg_match('/(\d+)\s*\+\s*(\d+)/', $value, $m)) {
                return (int) $m[1] + (int) $m[2];
            }
            if (preg_match('/(\d+)/', $value, $m)) {
                return (int) $m[1];
            }
            return null;
        }
        if (is_numeric($value)) return (int) $value;
        return null;
    }

    /**
     * Normalize storage size strings to GB (integer).
     * Accepts formats like "512", "512GB", "1 TB", "1.5TB", "1TB + 256GB".
     * - TB will be converted to GB using 1 TB = 1024 GB.
     * - If unit is omitted, assumes GB.
     */
    private function normalizeStorageSize($value): ?int
    {
        if ($value === null) return null;

        // If it's purely numeric, assume GB
        if (is_numeric($value)) {
            return (int) $value;
        }

        if (is_string($value)) {
            $str = strtolower(trim($value));

            // Split on '+' to allow summing parts like "1tb + 256gb"
            $parts = preg_split('/\s*\+\s*/', $str);
            $totalGb = 0.0;
            $found = false;

            foreach ($parts as $part) {
                // Extract first number and optional unit
                if (preg_match('/([0-9]*\.?[0-9]+)\s*(tb|gb)?/', $part, $m)) {
                    $num = (float) $m[1];
                    $unit = $m[2] ?? 'gb';
                    if ($unit === 'tb') {
                        $totalGb += $num * 1024.0; // Convert TB -> GB
                    } else {
                        $totalGb += $num; // Treat as GB
                    }
                    $found = true;
                }
            }

            if ($found) {
                return (int) round($totalGb);
            }

            // Fallback to integer extraction logic
            return $this->normalizeInt($value);
        }

        return null;
    }

    private function normalizeMoney($value): ?float
    {
        if ($value === null || $value === '') return null;
        if (is_string($value)) {
            $clean = preg_replace('/[^0-9.,-]/', '', $value);
            // Remove thousand separators (both . and ,), keep sign
            $clean = str_replace([',', '.'], ['', ''], $clean);
            if ($clean === '' || !is_numeric($clean)) return null;
            return (float) $clean;
        }
        if (is_numeric($value)) return (float) $value;
        return null;
    }

    private function normalizeSku($value): ?string
    {
        if (! $value) return null;
        return trim((string) $value);
    }
}
