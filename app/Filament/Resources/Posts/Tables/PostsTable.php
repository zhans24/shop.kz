<?php

// app/Filament/Resources/Posts/Tables/PostsTable.php
namespace App\Filament\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('cover')
                    ->label('Фото')
                    ->collection('cover')
                    ->conversion('thumb')
                    ->toggleable(),
                TextColumn::make('type')->label('Тип')->badge()->sortable(),
                TextColumn::make('title')->label('Заголовок')->searchable(),
                TextColumn::make('slug')->label('Slug')->searchable(),
                ToggleColumn::make('is_published')->label('Показывать'),
                TextColumn::make('published_at')->label('Дата')->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('sort')->label('Сорт.')->sortable(),
            ])
            ->recordActions([ EditAction::make() ])
            ->toolbarActions([ BulkActionGroup::make([ DeleteBulkAction::make() ]) ])
            ->defaultSort('published_at', 'desc');
    }
}
