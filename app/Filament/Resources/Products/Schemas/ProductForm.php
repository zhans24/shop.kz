<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()->tabs([
                Tabs\Tab::make('Основное')->schema([
                    Section::make()->schema([
                        TextInput::make('name')->label('Название')->required()->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state)))
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->regex('/^[a-z0-9\-]+$/')
                            ->helperText('Только латиница, цифры и дефис')
                            ->maxLength(100)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
                        Select::make('category_id')
                            ->label('Категория')
                            ->required()
                            ->relationship('category', 'name')
                            ->preload()
                            ->searchable(),

                        Select::make('brand_id')
                            ->label('Бренд')
                            ->relationship('brand', 'name')
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
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
                            ])->hint('Можно создать бренд , нажмите на плюс'),

                        RichEditor::make('description')->label('Описание')->columnSpanFull(),

                        TextInput::make('sku')->label('Артикул')->required()->maxLength(64)->columnSpanFull(),
                        TextInput::make('price')
                            ->label('Цена')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->suffix('₸'),
                        Toggle::make('is_published')->label('Опубликован')->default(false)->columnSpanFull(),
                        DateTimePicker::make('published_at')->label('Дата публикации')->time(false)->required()->columnSpanFull(),
                        TextInput::make('sort')
                            ->label('Позиция в сайте')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->step(1)
                            ->rule('integer'),
                        Toggle::make('is_hit')->label('Хит продаж')->default(false),
                        TextInput::make('hit_sort')->label('Сорт хита')->numeric()->minValue(1),
                        ])->columns(1),
                    Section::make('Скидка')->schema([
                        Toggle::make('discount_is_forever')
                            ->label('Бессрочно')
                            ->reactive(),

                        TextInput::make('discount_percent')
                            ->label('Скидка, %')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->nullable()
                            ->helperText('Оставьте пустым или 0, чтобы отключить скидку.'),

                        DateTimePicker::make('discount_starts_at')
                            ->label('Начало скидки')
                            ->seconds(false)
                            ->native(false)
                            ->helperText('Ввод в локальной таймзоне: ' . config('app.timezone')),

                        DateTimePicker::make('discount_ends_at')
                            ->label('Окончание скидки')
                            ->seconds(false)
                            ->native(false)
                            ->disabled(fn (callable $get) => (bool)$get('discount_is_forever'))
                            ->dehydrated(fn (callable $get, $state) => ! (bool)$get('discount_is_forever')),
                    ])->columns(4),

                ]),
                Tabs\Tab::make('Медиа')->schema([
                    Section::make('Обложка')->schema([
                        SpatieMediaLibraryFileUpload::make('cover')
                            ->label('Обложка')
                            ->collection('cover')
                            ->image()
                            ->maxFiles(1)
                            ->requiredWithout('images'),
                    ])->columns(1),

                    Section::make('Галерея')->schema([
                        SpatieMediaLibraryFileUpload::make('images')
                            ->label('Изображения')
                            ->collection('images')
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->panelLayout('grid')
                            ->hint('Перетащите, чтобы поменять порядок'),
                    ])->columns(1),
                ]),


                Tabs\Tab::make('SEO')->schema([
                    Section::make('SEO')->schema([
                        Group::make()->relationship('seo')->schema([
                            TextInput::make('meta_title')->label('Meta Title')->maxLength(255)->columnSpanFull(),
                            Textarea::make('meta_description')->label('Meta Description')->rows(3)->maxLength(300)->columnSpanFull(),
                            TextInput::make('h1')->label('H1')->maxLength(255)->columnSpanFull(),
                        ])->columns(1)->columnSpanFull(),
                    ])->columns(1),
                ]),
            ])->columnSpanFull()->persistTabInQueryString(),
        ]);
    }
}
