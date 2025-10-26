<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\DeliveryMethod;
use App\Models\PaymentMethod;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\{Section, Grid};
use Filament\Forms\Components\{Select, TextInput, Textarea, Placeholder};

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // ============ ОСНОВНОЕ ============
            Section::make('Данные клиента')
                ->columns(2)
                ->schema([
                    Select::make('status')
                        ->label('Статус заказа')
                        ->options([
                            'new'        => 'Новый',
                            'processing' => 'В обработке',
                            'done'       => 'Завершён',
                            'canceled'   => 'Отменён',
                        ])
                        ->required()
                        ->default('new')
                        ->columnSpan(1),

                    Select::make('customer_type')
                        ->label('Тип клиента')
                        ->options([
                            'private' => 'Физ. лицо',
                            'company' => 'Компания',
                        ])
                        ->default('private')
                        ->columnSpan(1),

                    TextInput::make('contact_name')
                        ->label('Имя / Компания')
                        ->placeholder('Например, Иван Иванов')
                        ->required()
                        ->columnSpanFull(),

                    TextInput::make('phone')
                        ->label('Телефон')
                        ->placeholder('+7 (___) ___-__-__')
                        ->tel()
                        ->required()
                        ->columnSpanFull(),

                    Textarea::make('address')
                        ->label('Адрес доставки')
                        ->placeholder('Город, улица, дом, квартира...')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),

            // ============ ДОСТАВКА И ОПЛАТА ============
            Section::make('Доставка и оплата')
                ->columns(2)
                ->schema([
                    Select::make('delivery_method_id')
                        ->label('Способ доставки')
                        ->options(fn () => DeliveryMethod::active()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if (!$state) return;
                            $dm = DeliveryMethod::find($state);
                            if ($dm) {
                                $set('delivery_method_name', $dm->name);
                                $set('shipping_total', (string) $dm->price);
                            }
                        }),

                    Select::make('payment_method_id')
                        ->label('Способ оплаты')
                        ->options(fn () => PaymentMethod::active()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if (!$state) return;
                            $pm = PaymentMethod::find($state);
                            if ($pm) {
                                $set('payment_method_name', $pm->name);
                            }
                        }),

                    Grid::make(3)
                        ->schema([
                            TextInput::make('delivery_method_name')
                                ->label('Название доставки')
                                ->disabled()
                                ->dehydrated(),

                            TextInput::make('payment_method_name')
                                ->label('Название оплаты')
                                ->disabled()
                                ->dehydrated(),

                            TextInput::make('shipping_total')
                                ->label('Стоимость доставки')
                                ->numeric()
                                ->default(0)
                                ->suffix('₸'),
                        ])
                        ->columnSpanFull(),
                ]),

            Section::make('Итоги заказа')
                ->columns(3)
                ->schema([
                    Placeholder::make('items_count')
                        ->label('Позиций')
                        ->content(fn ($record) => $record?->items_count ?? 0),

                    Placeholder::make('items_subtotal')
                        ->label('Сумма товаров')
                        ->content(fn ($record) => number_format($record?->items_subtotal ?? 0, 2, '.', ' ') . ' ₸'),

                    TextInput::make('shipping_total')
                        ->label('Стоимость доставки')
                        ->numeric()
                        ->default(0)
                        ->suffix('₸')
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                            // Мгновенный пересчёт total на форме (без сохранения)
                            $subtotal = (float) ($get('items_subtotal') ?? 0);
                            $set('total', number_format($subtotal + (float)$state, 2, '.', ''));
                        }),

                    Placeholder::make('total')
                        ->label('Итого к оплате')
                        ->content(fn ($record) => number_format($record?->total ?? 0, 2, '.', ' ') . ' ₸')
                        ->extraAttributes(['class' => 'text-xl font-bold text-green-500'])
                        ->columnSpanFull(),
                ]),

        ]);
    }
}
