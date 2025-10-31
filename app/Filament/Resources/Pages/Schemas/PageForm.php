<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\{RichEditor, TextInput, Textarea, Toggle, Repeater, Hidden, DateTimePicker};
use Filament\Forms\Components\SpatieMediaLibraryFileUpload as Media;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Основные данные')->schema([
                Grid::make(2)->schema([
                    TextInput::make('title')->label('Заголовок')->required(),
                    TextInput::make('slug')->label('Слаг')->required()->unique(ignoreRecord: true),
                ])->visible(false),
                Grid::make(3)->schema([
                    TextInput::make('template')
                        ->label('Шаблон')
                        ->required()
                        ->helperText('Напр.: home, about, delivery'),
                    Toggle::make('is_published')->label('Опубликовано')->default(true),
                    DateTimePicker::make('published_at')->label('Дата публикации')->seconds(false)->hidden(), // скрыто, вдруг пригодится
                ])->visible(false),
                Grid::make(2)->schema([
                    TextInput::make('meta_title')->label('Meta Title')->maxLength(70),
                    Textarea::make('meta_description')->label('Meta Description')->rows(2)->maxLength(300),
                ]),

            ])->columnSpanFull()->visible(fn ($get) => $get('template') !== 'header'),

            Section::make('Главная страница')
                ->schema([
                    TextInput::make('content.hero.decor_text')
                        ->label('Декор-текст (большой фоновый)')
                        ->helperText('Например, Создаем комфорт')
                        ->maxLength(100),

                    Repeater::make('content.hero.slides')
                        ->label('Слайды')
                        ->collapsible()
                        ->reorderable()
                        ->defaultItems(1)
                        ->schema([
                            Hidden::make('uid')->default(fn () => (string) Str::ulid()),
                            Grid::make(2)->schema([
                                TextInput::make('title')->label('Заголовок')->required()->maxLength(120),
                                Textarea::make('text')->label('Описание')->rows(3)->maxLength(500),
                            ]),
                            Grid::make(2)->schema([
                                Media::make('left_image')
                                    ->label('Левая картинка')
                                    ->collection(fn (Get $get) => 'hero_left_'  . $get('uid'))
                                    ->image()->maxFiles(1)->openable(),
                                Media::make('right_image')
                                    ->label('Правая картинка')
                                    ->collection(fn (Get $get) => 'hero_right_' . $get('uid'))
                                    ->image()->maxFiles(1)->openable(),
                            ]),
                        ])
                        ->columns(1)
                        ->columnSpanFull(),
                ])
                ->columnSpanFull()
                ->visible(fn ($get) => $get('template') === 'home'),

            Section::make('О компании (about)')
                ->schema([
                    TextInput::make('content.about.decor_text')
                        ->label('Декор-текст')->maxLength(100),

                    Media::make('about_media')
                        ->label('Картинка/видео секции')
                        ->collection('about_media')
                        ->acceptedFileTypes(['image/*', 'video/mp4', 'video/webm', 'video/quicktime'])
                        ->maxFiles(1)
                        ->openable(),

                    Grid::make(2)->schema([
                        TextInput::make('content.about.title')->label('Заголовок блока')->maxLength(120),
                        Textarea::make('content.about.text')->label('Текст о компании')->rows(5)->maxLength(3000),
                    ])->columnSpanFull(),

                    Repeater::make('content.about.benefits')
                        ->label('Преимущества')->reorderable()->defaultItems(4)
                        ->schema([
                            TextInput::make('title')->label('Заголовок')->required()->maxLength(80),
                            Textarea::make('text')->label('Текст')->rows(3)->maxLength(500),
                        ])
                        ->columns(1)->columnSpanFull(),

                    Repeater::make('content.about.reviews')
                        ->label('Отзывы')->reorderable()
                        ->schema([
                            Hidden::make('uid')->default(fn () => (string) Str::ulid()),
                            Grid::make(2)->schema([
                                TextInput::make('name')->label('Имя')->required()->maxLength(80),
                                Media::make('avatar')
                                    ->label('Аватар')
                                    ->collection(fn (Get $get) => 'about_review_' . $get('uid'))
                                    ->image()->maxFiles(1)->openable(),
                            ]),
                            Textarea::make('text')->label('Отзыв')->rows(4)->maxLength(600),
                        ])
                        ->columns(1)->columnSpanFull(),
                ])
                ->columns(1)->columnSpanFull()
                ->visible(fn ($get) => $get('template') === 'about'),


            Section::make('Доставка и оплата (delivery)')
                ->schema([
                    TextInput::make('content.delivery.decor_text')
                        ->label('Декор-текст')->maxLength(100),

                    Media::make('delivery_image')
                        ->label('Картинка секции')
                        ->collection('delivery_image')
                        ->image()->maxFiles(1),

                    TextInput::make('content.delivery.title')
                        ->label('Заголовок')->maxLength(120)->helperText('Напр.: Доставка и оплата'),

                    Repeater::make('content.delivery.points')
                        ->label('Пункты (список с галочками)')
                        ->reorderable()
                        ->schema([
                            Textarea::make('text')->label('Текст')->rows(3)->required()->maxLength(500),
                        ])
                        ->defaultItems(4)
                        ->columns(1)->columnSpanFull(),
                ])
                ->columns(1)->columnSpanFull()
                ->visible(fn ($get) => $get('template') === 'delivery'),

            Section::make('Политика конфиденциальности (privacy)')
                ->schema([
                    TextInput::make('content.privacy.title')
                        ->label('Заголовок H1')->maxLength(180)
                        ->placeholder('Политика конфиденциальности'),

                    RichEditor::make('content.privacy.body')
                        ->label('Текст политики')
                        ->toolbarButtons([
                            'bold','italic','underline','strike',
                            'h2','h3','blockquote','orderedList','bulletList',
                            'link','undo','redo','codeBlock',
                        ])
                        ->columnSpanFull()
                        ->required(),
                ])
                ->columns(1)
                ->columnSpanFull()
                ->visible(fn ($get) => $get('template') === 'privacy'),

            Section::make('Header (города)')
                ->schema([
                    Repeater::make('content.header.cities')
                        ->label('Города')
                        ->helperText('Будут показаны в выпадающем списке города в шапке. Один может быть по умолчанию.')
                        ->reorderable()
                        ->collapsible()
                        ->defaultItems(2)
                        ->schema([
                            Grid::make(3)->schema([
                                TextInput::make('name')
                                    ->label('Название')
                                    ->placeholder('Алматы')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set) {
                                        $set('slug', Str::slug((string) $state));
                                    }),

                                TextInput::make('slug')
                                    ->label('Слаг')
                                    ->placeholder('almaty')
                                    ->helperText('Для URL / сессии')
                                    ->required(),

                                TextInput::make('sort')
                                    ->label('Сортировка')
                                    ->numeric()
                                    ->helperText('Номер позиции при показе')
                                    ->placeholder('100'),
                            ]),
                        ])
                ])
                ->columnSpanFull()
                ->visible(fn ($get) => $get('template') === 'header'),

        ]);


    }
}
