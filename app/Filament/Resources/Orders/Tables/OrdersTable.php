<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
// ...
            ->columns([
                TextColumn::make('order_number')
                    ->label('№')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->colors([
                        'warning' => 'new',
                        'info'    => 'processing',
                        'success' => 'done',
                        'danger'  => 'canceled',
                    ])
                    ->sortable(),

                TextColumn::make('contact_name')->label('Клиент')->searchable(),
                TextColumn::make('phone')->label('Телефон')->searchable(),

                TextColumn::make('total')->label('Итого')->money('KZT', true)->sortable(),

                TextColumn::make('ordered_at')
                    ->label('Дата заказа')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('id', 'desc')
            ->recordActions([
                EditAction::make()->label('Редактировать'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Удалить'),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }
}
