<?php

namespace App\Filament\Resources\Permissions;

use App\Filament\Resources\Permissions\PermissionResource\Pages\CreatePermission;
use App\Filament\Resources\Permissions\PermissionResource\Pages\EditPermission;
use App\Filament\Resources\Permissions\PermissionResource\Pages\ListPermissions;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use UnitEnum;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?int $navigationSort = 21;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            \Filament\Forms\Components\TextInput::make('name')
                ->label('Permission')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            \Filament\Forms\Components\Hidden::make('guard_name')
                ->default(config('auth.defaults.guard', 'web')),
            \Filament\Forms\Components\Select::make('roles')
                ->label('Roles')
                ->relationship('roles', 'name')
                ->multiple()
                ->preload()
                ->searchable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')->label('Permission')->searchable()->sortable(),
                \Filament\Tables\Columns\BadgeColumn::make('roles.name')
                    ->label('Roles')
                    ->separator(', ')
                    ->limitList(3),
                \Filament\Tables\Columns\TextColumn::make('created_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => PermissionResource\Pages\ListPermissions::route('/'),
            'create' => PermissionResource\Pages\CreatePermission::route('/create'),
            'edit' => PermissionResource\Pages\EditPermission::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user && method_exists($user, 'hasRole') && $user->hasRole('admin');
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit($record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete($record): bool
    {
        return static::canViewAny();
    }
}

namespace App\Filament\Resources\Permissions\PermissionResource\Pages;

use App\Filament\Resources\Permissions\PermissionResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;

class ListPermissions extends ListRecords
{
    protected static string $resource = PermissionResource::class;
}

class CreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;
}

class EditPermission extends EditRecord
{
    protected static string $resource = PermissionResource::class;
}
