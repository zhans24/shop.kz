<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class StocksAvailableRelationManager extends RelationManager
{
    protected static string $relationship = 'stocks';
    protected static ?string $title = 'Наличие по городам';
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('city_name')
                ->label('Город')
                ->required()
                ->maxLength(100),

            Toggle::make('is_available')
                ->label('Есть в наличии')
                ->default(false),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('city_name')->label('Город')->searchable(),
                ToggleColumn::make('is_available')
                    ->label('Доступно')
                    ->afterStateUpdated(fn() => null),
            ])
            ->headerActions([
                CreateAction::make()->label('Добавить город'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
