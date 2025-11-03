<?php

namespace App\Filament\Resources\Blogs\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Auth;

class BlogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                FileUpload::make('image')
                    ->label('Cover Image')
                    ->image()
                    ->disk('public')
                    ->directory('blogs')
                    ->visibility('public')
                    ->imageEditor()
                    ->imageCropAspectRatio('16:9')
                    ->maxSize(1024)
                    ->imagePreviewHeight('200')
                    ->downloadable()
                    ->columnSpanFull()
                    ->openable(),

                TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                // Slug is auto-generated in CreateBlog page; hide input to avoid manual edits/validation conflicts
                TextInput::make('slug')
                    ->disabled()
                    ->dehydrated(false)
                    ->hidden(),

                Select::make('created_by')
                    ->label('Author')
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload()
                    ->default(fn () => Auth::id())
                    ->disabled()
                    ->dehydrated(false)
                    ->hidden(),


                RichEditor::make('content')
                    ->label('Content')
                    ->required()
                    ->columnSpanFull()
                    ->toolbarButtons([
                        ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                        ['h1', 'h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd', 'alignJustify'],
                        ['bulletList', 'orderedList'],
                        ['table', 'attachFiles'], // The `customBlocks` and `mergeTags` tools are also added here if those features are used.
                        ['undo', 'redo'],
                    ])
                    ->extraInputAttributes(['style' => 'min-height: 24rem; text-align: justify;']),

                Toggle::make('is_published')
                    ->label('Published')
                    ->default(false),
            ]);
    }
}
