<?php

// app/Filament/Resources/Posts/PostResource.php
namespace App\Filament\Resources\Posts;

use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Filament\Resources\Posts\Schemas\PostForm;
use App\Filament\Resources\Posts\Tables\PostsTable;
use App\Models\Post;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|null|\UnitEnum $navigationGroup  = 'Контент';
    protected static string|null|BackedEnum $navigationIcon  = 'heroicon-o-megaphone';
    protected static ?int    $navigationSort = 21;
    protected static ?string $modelLabel       = 'Новость / Акция';
    protected static ?string $pluralModelLabel = 'Новости / акции';

    public static function form(Schema $schema): Schema     { return PostForm::configure($schema); }
    public static function table(Table $table): Table       { return PostsTable::configure($table); }
    public static function getRelations(): array            { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit'   => EditPost::route('/{record}/edit'),
        ];
    }
}
