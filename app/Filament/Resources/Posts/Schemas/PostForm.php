<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use App\Models\Post;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Основное')->schema([
                Select::make('type')
                    ->label('Тип')
                    ->options(['news'=>'Новость','promo'=>'Акция'])
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('title')
                    ->label('Заголовок')
                    ->required()
                    ->reactive()
                    ->debounce(400)
                    ->afterStateUpdated(function (?string $state, Set $set, Get $get) {
                        // если ручной режим выключен — обновляем slug
                        if (! $get('slug_manual')) {
                            $set('slug', Str::slug((string) $state));
                        }
                    })
                    ->columnSpanFull(),

                Toggle::make('slug_manual')
                    ->label('Править slug вручную')
                    ->helperText('Выключено — slug генерится из заголовка; включи, чтобы править вручную')
                    ->default(false)
                    ->live()
                    ->columnSpanFull(),

                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->rule('filled')
                    ->regex('/^[a-z0-9\-]+$/')
                    ->maxLength(100)
                    ->unique(
                        table: Post::class,
                        column: 'slug',
                        ignorable: fn (?Model $record) => $record,
                    )
                    ->validationMessages([
                        'unique' => 'Такой slug уже существует. Включи «Править slug вручную» и задай другой.',
                        'regex'  => 'Только латиница, цифры и дефис.',
                        'filled' => 'Slug обязателен.',
                    ])
                    ->reactive()
                    ->debounce(300)
                    ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug((string) $state)))
                    ->disabled(fn ($get) => ! $get('slug_manual'))
                    ->columnSpanFull(),

        RichEditor::make('content')->label('Контент')->columnSpanFull(),
                Textarea::make('excerpt')->label('Краткое описание')->maxLength(500)->columnSpanFull(),

                SpatieMediaLibraryFileUpload::make('cover')
                    ->label('Обложка')
                    ->collection('cover')
                    ->columnSpanFull(),

                Toggle::make('is_published')->label('Опубликовано')->default(false)->columnSpanFull(),
                DateTimePicker::make('published_at')->label('Дата публикации')->seconds(false)->columnSpanFull(),

                TextInput::make('sort')->label('Позиция в сайте')->numeric()->default(1)->minValue(1)->step(1),
            ])->columns(1),

            Section::make('SEO')->schema([
                Group::make()->relationship('seo')->schema([
                    TextInput::make('meta_title')->label('Meta Title')->maxLength(255)->columnSpanFull(),
                    Textarea::make('meta_description')->label('Meta Description')->rows(3)->maxLength(300)->columnSpanFull(),
                    TextInput::make('h1')->label('H1')->maxLength(255)->columnSpanFull(),
                ])->columns(1)->columnSpanFull(),
            ])->columns(1),
        ]);
    }
}
