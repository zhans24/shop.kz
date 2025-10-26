<?php

namespace App\Filament\Widgets;

use App\Models\DeliveryMethod;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class DeliveryMethodsTable extends BaseWidget
{

    protected static ?string $heading = 'Способы доставки';
    protected int | string | array $columnSpan = 'full';

    protected static bool $isDiscovered = false;


    public function table(Table $table): Table
    {
        return $table
            ->query(DeliveryMethod::query())
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Название')->searchable(),
                Tables\Columns\TextColumn::make('price')->label('Цена')->money('KZT', true)->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Активен')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->label('Обновлён')->dateTime('Y-m-d H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Активные'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Добавить')
                    ->modalHeading('Новый способ доставки')
                    ->schema($this->formSchema())
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading('Редактировать доставку'),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('Нет способов доставки')
            ->emptyStateDescription('Нажмите «Добавить», чтобы создать первый способ.')
            ->defaultSort('id', '');
    }

    protected function formSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->label('Название')
                ->required()
                ->maxLength(190)
                ->unique(ignoreRecord: true),

            Forms\Components\TextInput::make('price')
                ->label('Цена')
                ->numeric()
                ->minValue(0)
                ->required()
                ->suffix('₸'),

            Forms\Components\Toggle::make('is_active')
                ->label('Активен')
                ->default(true),
        ];
    }


}
