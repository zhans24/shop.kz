<?php

namespace App\Filament\Resources\Categories\Tables;

use App\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn; // ← добавили
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->collection('image')
                    ->conversion('thumb')
                    ->label('Фото')         ,
                TextColumn::make('name')->label('Название')->searchable(),
                TextColumn::make('slug')->label('Slug')->searchable(),

                ToggleColumn::make('is_visible')
                    ->label('Активна')
                    ->onIcon('heroicon-m-check')
                    ->offIcon('heroicon-m-x-mark')
                    ->afterStateUpdated(fn (Category $record, bool $state) => $record->save()),

                ToggleColumn::make('is_popular')
                    ->label('Популярная')
                    ->onIcon('heroicon-m-star')
                    ->offIcon('heroicon-m-star')
                    ->afterStateUpdated(fn (Category $record, bool $state) => $record->save()),
                TextColumn::make('sort')->label('Сорт.')->sortable(),
                TextColumn::make('created_at')->label('Создано')->dateTime('d.m.Y H:i')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([ EditAction::make() ])
            ->toolbarActions([ BulkActionGroup::make([ DeleteBulkAction::make() ]) ])
            ->defaultSort('sort');
    }
}
