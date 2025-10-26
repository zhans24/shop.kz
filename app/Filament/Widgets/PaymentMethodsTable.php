<?php

namespace App\Filament\Widgets;

use App\Models\PaymentMethod;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PaymentMethodsTable extends BaseWidget
{

    protected static ?string $heading = "Способы оплаты";
    protected int | string | array $columnSpan = 'full';
    protected static bool $isDiscovered = false;


    public function table(Table $table): Table
    {
        return $table
            ->query(PaymentMethod::query())
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Название')->searchable(),
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
                    ->modalHeading('Новый способ оплаты')
                    ->form($this->formSchema()),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading('Редактировать оплату')
                    ->form($this->formSchema()),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('Нет способов оплаты')
            ->emptyStateDescription('Нажмите «Добавить», чтобы создать первый способ.')
            ->defaultSort('id', 'desc');
    }

    protected function formSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->label('Название')
                ->required()
                ->maxLength(190)
                ->unique(ignoreRecord: true),

            Forms\Components\Toggle::make('is_active')
                ->label('Активен')
                ->default(true),
        ];
    }


}
