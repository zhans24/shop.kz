<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Основное')->schema([
                Select::make('type')->label('Тип')->options(['news'=>'Новость','promo'=>'Акция'])->required()->columnSpanFull(),

                TextInput::make('title')->label('Заголовок')->required()->columnSpanFull(),

                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->regex('/^[a-z0-9\-]+$/')
                    ->helperText('Только латиница, цифры и дефис')
                    ->maxLength(100)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),


                RichEditor::make('content')->label('Контент')->columnSpanFull(),

                SpatieMediaLibraryFileUpload::make('cover')->label('Обложка')->collection('cover')->columnSpanFull(),

                Toggle::make('is_published')->label('Опубликовано')->default(false)->columnSpanFull(),

                DateTimePicker::make('published_at')->label('Дата публикации')->columnSpanFull(),

                TextInput::make('sort')

                    ->label('Позиция в сайте')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->step(1)
                    ->rule('integer'),
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
