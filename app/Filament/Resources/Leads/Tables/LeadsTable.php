<?php

namespace App\Filament\Resources\Leads\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('name')->label('Имя')->searchable(),
                TextColumn::make('phone')->label('Телефон'),
                TextColumn::make('email')->label('Email'),
                BadgeColumn::make('status')
                    ->label('Статус')
                    ->colors([
                        'gray' => 'new',
                        'warning' => 'in_progress',
                        'success' => 'done',
                    ])
                    ->formatStateUsing(fn($state) => match($state) {
                        'new' => 'Новый',
                        'in_progress' => 'В работе',
                        'done' => 'Завершён',
                        default => ucfirst($state),
                    }),
                TextColumn::make('created_at')->label('Создан')->dateTime('Y-m-d H:i'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'new' => 'Новый',
                        'in_progress' => 'В работе',
                        'done' => 'Завершён',
                    ]),
            ])
            ->defaultSort('id', 'asc')
            ->recordActions([
                EditAction::make()->label('Редактировать'),
                DeleteAction::make()->label('Удалить'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Удалить'),
                ]),
            ]);
    }
}
