<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Название')
                    ->required()
                    ->minLength(2)
                    ->maxLength(255)
                    ->helperText('Название от 2 символов, без спецсимволов')
                    ->live(onBlur: true),

                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->regex('/^[a-z0-9\-]+$/')
                    ->helperText('Только латиница, цифры и дефис')
                    ->maxLength(100)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),

                TextInput::make('country')->label('Страна производителя'),

                Toggle::make('is_visible')->label('Показывать')
                    ->required()->default(true),

                TextInput::make('sort')
                    ->label('Сортировка')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->step(1)
                    ->rule('integer'),
            ]);
    }
}
