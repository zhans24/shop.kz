<?php

namespace App\Filament\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')->label('Тип')->badge()->sortable(),
                TextColumn::make('title')->label('Заголовок')->searchable(),
                TextColumn::make('slug')->label('Slug')->searchable(),
                IconColumn::make('is_published')->label('ON')->boolean(),
                TextColumn::make('published_at')->label('Дата')->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('sort')->label('Сорт.')->sortable(),
            ])
            ->recordActions([ EditAction::make() ])
            ->toolbarActions([ BulkActionGroup::make([ DeleteBulkAction::make() ]) ])
            ->defaultSort('published_at', 'desc');
    }
}
