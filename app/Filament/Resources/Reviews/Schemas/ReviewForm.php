<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('product_id')
                ->label('Товар')
                ->relationship('product', 'name')
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('author_name')
                ->label('Автор')
                ->maxLength(255)
                ->required(),

            Textarea::make('body')
                ->label('Текст отзыва')
                ->rows(6)
                ->columnSpanFull()
                ->required(),

            SpatieMediaLibraryFileUpload::make('photos')
                ->label('Фото')
                ->collection('photos')
                ->multiple()
                ->reorderable()
                ->panelLayout('grid'),

            Toggle::make('is_approved')
                ->label('Одобрен')
                ->default(false),
        ])->columns(1);
    }
}
