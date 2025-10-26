<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;

class ProductsTable
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


                TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('brand.name')
                    ->label('Бренд')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('category.name')
                    ->label('Категория')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('price')
                    ->label('Цена')
                    ->money('KZT', true)
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label('Публ.')
                    ->boolean(),

                BadgeColumn::make('discount_percent')
                    ->label('Скидка')
                    ->formatStateUsing(fn ($state, $record) => $record->hasActiveDiscount() ? ($record->discount_percent . '%') : '—')
                    ->colors(fn ($record) => $record->hasActiveDiscount() ? ['success'] : ['secondary'])
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Обновлён')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('brand_id')
                    ->label('Бренд')
                    ->relationship('brand', 'name'),

                SelectFilter::make('category_id')
                    ->label('Категория')
                    ->relationship('category', 'name'),

                TernaryFilter::make('is_published')
                    ->label('Опубликован'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }
}
