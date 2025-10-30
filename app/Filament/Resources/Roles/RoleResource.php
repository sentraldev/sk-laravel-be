<?php

namespace App\Filament\Resources\Roles;

use App\Filament\Resources\Roles\RoleResource\Pages\CreateRole;
use App\Filament\Resources\Roles\RoleResource\Pages\EditRole;
use App\Filament\Resources\Roles\RoleResource\Pages\ListRoles;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use UnitEnum;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            \Filament\Forms\Components\TextInput::make('name')
                ->label('Role Name')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            \Filament\Forms\Components\Hidden::make('guard_name')
                ->default(config('auth.defaults.guard', 'web')),
            \Filament\Forms\Components\Select::make('permissions')
                ->label('Permissions')
                ->relationship('permissions', 'name')
                ->multiple()
                ->preload()
                ->searchable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')->label('Role')->searchable()->sortable(),
                \Filament\Tables\Columns\BadgeColumn::make('permissions.name')
                    ->label('Permissions')
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
            'index' => RoleResource\Pages\ListRoles::route('/'),
            'create' => RoleResource\Pages\CreateRole::route('/create'),
            'edit' => RoleResource\Pages\EditRole::route('/{record}/edit'),
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

namespace App\Filament\Resources\Roles\RoleResource\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;
}

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;
}

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;
}
