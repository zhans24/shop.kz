<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Models\Category;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $title = 'Позиции заказа';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('sku')->label('SKU')->maxLength(100),
            TextInput::make('name')->label('Название')->required(),
            TextInput::make('qty')->label('Кол-во')->numeric()->minValue(1)->default(1)->reactive()
                ->afterStateUpdated(fn ($state, $set, $get) => $set('total', (string)((float)$get('price') * (float)$state))),
            TextInput::make('price')->label('Цена')->numeric()->minValue(0)->default(0)->reactive()
                ->afterStateUpdated(fn ($state, $set, $get) => $set('total', (string)((float)$get('qty') * (float)$state))),
            TextInput::make('total')->label('Сумма')->numeric()->disabled(),
        ])->columns(5);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')->label('SKU')->toggleable(),
                Tables\Columns\TextColumn::make('name')->label('Название')->searchable(),
                Tables\Columns\TextColumn::make('qty')->label('Кол-во')->sortable(),
                Tables\Columns\TextColumn::make('price')->label('Цена')->money('KZT', true)->sortable(),
                Tables\Columns\TextColumn::make('total')->label('Сумма')->money('KZT', true)->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->label('Обновлён')->dateTime('Y-m-d H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make()->label('Добавить позицию')
                    ->after(fn () => $this->recalcOrderTotals()),

                Action::make('addFromCatalog')
                    ->label('Добавить из каталога')
                    ->icon('heroicon-o-plus')
                    ->slideOver()
                    ->schema([
                        Select::make('category_id')
                            ->label('Категория')
                            ->searchable()
                            ->preload()
                            ->options(fn () => Category::orderBy('name')->pluck('name', 'id'))
                            ->reactive()
                            ->required(),

                        Select::make('product_id')
                            ->label('Товар')
                            ->searchable()
                            ->reactive()
                            ->options(function (callable $get) {
                                $catId = $get('category_id');
                                if (!$catId) return [];
                                return Product::where('category_id', $catId)
                                    ->orderBy('name')->limit(500)
                                    ->pluck('name', 'id');
                            })
                            ->required(),

                        TextInput::make('qty')->label('Кол-во')->numeric()->minValue(1)->default(1)->required(),
                    ])
                    ->action(function (array $data) {
                        $order   = $this->getOwnerRecord();
                        $product = Product::findOrFail($data['product_id']);

                        $qty   = max(1, (int)($data['qty'] ?? 1));
                        $price = (float) ($product->min_price ?? 0);
                        $sku   = $product->slug; // или null

                        // Мердж по product_id внутри заказа
                        $existing = $order->items()->where('product_id', $product->id)->first();

                        if ($existing) {
                            $existing->qty += $qty;
                            $existing->price = $price; // на всякий
                            $existing->save();
                        } else {
                            $order->items()->create([
                                'product_id' => $product->id,
                                'sku'        => $sku,
                                'name'       => $product->name,
                                'qty'        => $qty,
                                'price'      => $price,
                            ]);
                        }

                        $order->refresh();
                        $order->recalcTotals();

                        Notification::make()->title("Добавлено: {$product->name}")->success()->send();
                    }),
            ])
            ->recordActions([
                EditAction::make()->after(fn () => $this->recalcOrderTotals()),
                DeleteAction::make()->after(fn () => $this->recalcOrderTotals()),
            ]);
    }

    protected function recalcOrderTotals(): void
    {
        $order = $this->getOwnerRecord();
        $order->refresh();
        $order->recalcTotals();
        Notification::make()->title('Итоги заказа пересчитаны')->success()->send();
    }
}
