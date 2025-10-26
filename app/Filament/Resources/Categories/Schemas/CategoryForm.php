<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload; // ← добавили
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Основное')->schema([
                SpatieMediaLibraryFileUpload::make('image') // одно изображение
                ->collection('image')                  // коллекция как в модели
                ->label('Обложка')
                    ->image()
                    ->openable()
                    ->columnSpanFull(),

                TextInput::make('name')->label('Название')->required()->columnSpanFull(),

                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->regex('/^[a-z0-9\-]+$/')
                    ->helperText('Только латиница, цифры и дефис')
                    ->maxLength(100)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),

                Toggle::make('is_visible')->label('Отображать на сайте')->default(true)->columnSpanFull(),
                TextInput::make('sort')
                    ->label('Позиция в сайте')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->step(1)
                    ->rule('integer'),
                Toggle::make('is_popular')
                    ->label('Популярная категория (выводить на главной)')
                    ->helperText('Будет показана в блоке «Категории товаров».')->default(true)
                    ->columnSpanFull(),
            ])->columns(1),


            Section::make('SEO')->schema([
                Group::make()->relationship('seo')->schema([
                    TextInput::make('meta_title')->label('Meta Title')->maxLength(255)->columnSpanFull(),
                    \Filament\Forms\Components\Textarea::make('meta_description')->label('Meta Description')->rows(3)->maxLength(300)->columnSpanFull(),
                    TextInput::make('h1')->label('H1')->maxLength(255)->columnSpanFull(),
                ])->columns(1)->columnSpanFull(),
            ])->columns(1),
        ]);
    }
}
