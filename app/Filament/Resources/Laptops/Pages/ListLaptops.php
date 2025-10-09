<?php

namespace App\Filament\Resources\Laptops\Pages;

use App\Filament\Resources\Laptops\LaptopResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

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
        ];
    }
}
