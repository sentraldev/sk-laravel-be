<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;
    protected static string|UnitEnum|null $navigationGroup = 'System';

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        // Hide the protected super admin account from listings for everyone except the super admin themselves
        $user = Auth::user();
        $query = parent::getEloquentQuery();

        if (! $user || $user->email !== 'admin@sentralkomputer.com') {
            $query->where('email', '!=', 'admin@sentralkomputer.com');
        }

        return $query;
    }

    // Authorization: permission-gated (manage users)
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && method_exists($user, 'can') && $user->can('manage users');
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canView($record): bool
    {
        $user = Auth::user();
        if ($record && isset($record->email) && $record->email === 'admin@sentralkomputer.com') {
            // Only the super admin themselves can view their record
            return $user && $user->email === 'admin@sentralkomputer.com';
        }
        return static::canViewAny();
    }

    public static function canEdit($record): bool
    {
        $user = Auth::user();
        if ($record && isset($record->email) && $record->email === 'admin@sentralkomputer.com') {
            // Only the super admin themselves can edit their record
            return $user && $user->email === 'admin@sentralkomputer.com';
        }
        return static::canViewAny();
    }

    public static function canDelete($record): bool
    {
        $user = Auth::user();
        if ($record && isset($record->email) && $record->email === 'admin@sentralkomputer.com') {
            // Avoid accidental deletion of the super admin account; only allow self-delete if desired
            return $user && $user->email === 'admin@sentralkomputer.com';
        }
        return static::canViewAny();
    }
}
