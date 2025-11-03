<?php

namespace App\Filament\Resources\Blogs;

use App\Filament\Resources\Blogs\Pages\CreateBlog;
use App\Filament\Resources\Blogs\Pages\EditBlog;
use App\Filament\Resources\Blogs\Pages\ListBlogs;
use App\Filament\Resources\Blogs\Schemas\BlogForm;
use App\Filament\Resources\Blogs\Tables\BlogsTable;
use App\Models\Blog;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup = 'Marketing';

    public static function form(Schema $schema): Schema
    {
        return BlogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BlogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBlogs::route('/'),
            'create' => CreateBlog::route('/create'),
            'edit' => EditBlog::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = Auth::user();
        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

    // Permission-gated: allow full access if user can manage blogs
    $canViewAll = method_exists($user, 'can') && $user->can('manage blogs');

        if ($canViewAll) {
            return $query;
        }

        // Otherwise restrict to the current user's posts
        return $query->where('created_by', $user->id);
    }


    // Authorization: permission-gated
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (! $user || ! method_exists($user, 'can')) {
            return false;
        }
        // If user has any of these, they can access the listing/UI
        return $user->can('manage blogs') || $user->can('create blogs') || $user->can('delete blogs');
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && method_exists($user, 'can') && ($user->can('manage blogs') || $user->can('create blogs'));
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();
        return $user && method_exists($user, 'can') && ($user->can('manage blogs') || $user->can('create blogs'));
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();
        return $user && method_exists($user, 'can') && ($user->can('manage blogs') || $user->can('delete blogs'));
    }
}
