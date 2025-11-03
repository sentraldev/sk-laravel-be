<?php

namespace App\Filament\Resources\Blogs\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Facades\Auth;
use App\Models\BlogCategory;

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

                Grid::make(4)->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(3),

                    Select::make('blog_category_id')
                        ->label('Category')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            TextInput::make('name')
                                ->label('Category Name')
                                ->required()
                                ->maxLength(100),
                        ])
                        ->required()
                        ->createOptionUsing(fn (array $data) => BlogCategory::create($data)->getKey())
                        ->placeholder('Select category')
                        ->columnSpan(1),
                ])->columnSpanFull(),

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

                // Slug is auto-generated in CreateBlog page; hide input to avoid manual edits/validation conflicts
                TextInput::make('slug')
                    ->disabled()
                    ->dehydrated(false)
                    ->hidden(),
            ]);
    }
}
