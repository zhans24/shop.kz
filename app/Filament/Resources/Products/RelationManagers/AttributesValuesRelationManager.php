<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttributesValuesRelationManager extends RelationManager
{
    protected static string $relationship = 'attributesValues';
    protected static ?string $title = 'Характеристики';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('attribute_id')
                ->label('Атрибут')
                ->options(fn () => Attribute::query()->orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->required()
                ->reactive()
                ->afterStateUpdated(fn ($state, $set) => $set('attribute_value_id', null)),

            Select::make('attribute_value_id')
                ->label('Значение (из списка)')
                ->options(function ($get) {
                    $attrId = $get('attribute_id');
                    if (! $attrId) return [];
                    return AttributeValue::query()
                        ->where('attribute_id', $attrId)
                        ->orderBy('value')
                        ->pluck('value', 'id');
                })
                ->searchable()
                ->native(false)
                ->helperText('Если нужного значения нет в списке — заполни поле ниже “Произвольное значение”.')
                ->nullable(),

            TextInput::make('value')
                ->label('Произвольное значение')
                ->helperText('Используется, если значение не выбрано из списка.'),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            // тут нет "name" у записи — пусть заголовком будет атрибут
            ->recordTitleAttribute('attribute.name')
            ->columns([
                TextColumn::make('attribute.name')
                    ->label('Атрибут')
                    ->searchable(),

                TextColumn::make('attributeValue.value')
                    ->label('Значение (список)')
                    ->toggleable(),

                TextColumn::make('value')
                    ->label('Значение (ручное)')
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i'),
            ])
            ->headerActions([
                CreateAction::make()->label('Добавить характеристику'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
