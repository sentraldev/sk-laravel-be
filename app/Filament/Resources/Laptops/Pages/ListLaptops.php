<?php

namespace App\Filament\Resources\Laptops\Pages;

use App\Filament\Resources\Laptops\LaptopResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;

class ListLaptops extends ListRecords
{
    protected static string $resource = LaptopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('import')
                ->label('Import Laptops')
                ->icon('heroicon-o-arrow-up-on-square')
                ->form([
                    FileUpload::make('file')
                        ->label('Excel/CSV File')
                        ->preserveFilenames()
                        ->acceptedFileTypes(['text/csv','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.ms-excel'])
                        ->directory('imports')
                        ->required(),
                ])
                ->action(function (array $data) {
                    // Defer to importer job/class
                    \App\Services\Imports\LaptopImporter::import($data['file']);
                    Notification::make()
                        ->title('Import started')
                        ->body('Check the Laptops table for new records.')
                        ->success()
                        ->send();
                }),
            Action::make('importImages')
                ->label('Import Images by Filename (sku-x.png)')
                ->icon('heroicon-o-photo')
                ->form([
                    FileUpload::make('images')
                        ->label('Image files')
                        ->helperText('Upload files named like SKU-1.jpg, SKU-2.png. They will be attached to the product matching the SKU (case-insensitive).')
                        ->multiple()
                        ->image()
                        ->preserveFilenames()
                        ->disk('public')
                        ->directory('imports/products')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $paths = (array) ($data['images'] ?? []);
                    if (empty($paths)) {
                        Notification::make()->title('No files uploaded')->danger()->send();
                        return;
                    }

                    $disk = 'public';
                    $processed = 0;
                    $attached = 0;
                    $skipped = 0;

                    foreach ($paths as $path) {
                        $filename = basename($path);
                        $nameNoExt = pathinfo($filename, PATHINFO_FILENAME);
                        // Match pattern {sku}-{index}
                        if (! preg_match('/^(.+)-(\d+)$/', $nameNoExt, $m)) {
                            $skipped++;
                            continue;
                        }
                        $sku = trim($m[1]);
                        if ($sku === '') {
                            $skipped++;
                            continue;
                        }

                        // Find product by exact SKU, else case-insensitive
                        $product = \App\Models\Product::where('sku', $sku)
                            ->first() ?: \App\Models\Product::whereRaw('LOWER(sku) = ?', [strtolower($sku)])->first();
                        if (! $product) {
                            $skipped++;
                            continue;
                        }

                        $destDir = 'products/' . $sku;
                        $destPath = $destDir . '/' . $filename;
                        if (! Storage::disk($disk)->exists($destDir)) {
                            Storage::disk($disk)->makeDirectory($destDir);
                        }

                        // Move file into products/{sku}/ if not already there
                        if ($path !== $destPath) {
                            if (Storage::disk($disk)->exists($destPath)) {
                                // If destination exists, skip moving to avoid overwrite
                                $skipped++;
                            } else {
                                Storage::disk($disk)->move($path, $destPath);
                            }
                        }

                        // Attach to product images
                        $existing = is_array($product->images ?? null) ? $product->images : [];
                        if (! in_array($destPath, $existing, true)) {
                            $existing[] = $destPath;
                            $product->images = array_values($existing);
                            $product->save();
                            $attached++;
                        }
                        $processed++;
                    }

                    Notification::make()
                        ->title('Image import finished')
                        ->body("Processed {$processed} file(s). Attached {$attached}, Skipped {$skipped}.")
                        ->success()
                        ->send();
                }),
        ];
    }
}
 
