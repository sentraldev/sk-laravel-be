<?php

namespace App\Services\Imports;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class LaptopImporter
{
    /**
     * Import laptops from an uploaded file path (relative to storage disk).
     * Supports CSV/XLSX. Expected columns: name, brand, processor, gpu, ram_size, storage_size.
     */
    public static function import(string $filePath): void
    {
        $disk = config('filesystems.default', 'public');
        $fullPath = Storage::disk($disk)->path($filePath);

        Excel::import(new LaptopRowsImport, $fullPath);
    }
}
