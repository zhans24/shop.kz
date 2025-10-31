<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;   // ⬅ импорт
use Filament\Forms\Components\Hidden;        // ⬅ импорт
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                ->relationship('attribute','name')
                ->required()
                ->live()
                ->createOptionForm([
                    TextInput::make('name')->label('Название')->required(),
                    TextInput::make('code')
                        ->label('Код')->required()
                        ->helperText('Напр. color, material, length')
                        ->regex('/^[a-z0-9\-_]+$/'),
                    Select::make('type')->label('Тип')->required()
                        ->options(['text'=>'Текст','number'=>'Число','bool'=>'Да/Нет'])->default('text'),
                    Toggle::make('is_filterable')->label('Фильтруемый')->default(true),
                    TextInput::make('unit')->label('Ед. изм.')->helperText('Напр. мм, кг'),
                    TextInput::make('sort')->numeric()->default(1),
                ])
                ->afterStateUpdated(fn($state, $set) => $set('attribute_value_id', null)),

            Select::make('attribute_value_id')
                ->label('Значение (из списка)')
                ->options(function ($get) {
                    $attrId = $get('attribute_id');
                    if (!$attrId) return [];
                    return AttributeValue::query()
                        ->where('attribute_id', $attrId)
                        ->orderBy('value')
                        ->pluck('value', 'id');
                })
                ->searchable()
                ->native(false)
                ->nullable()
                ->helperText('Нет в списке? Заполни «Произвольное значение».')
                // Форма создания новой опции
                ->createOptionForm(function ($get) {
                    return [
                        Hidden::make('attribute_id')->default(fn () => $get('attribute_id')),
                        TextInput::make('value')
                            ->label('Значение')->required()
                            ->helperText(Attribute::find($get('attribute_id'))?->code === 'color' ? 'Напр. Чёрный' : null),
                        ColorPicker::make('slug')
                            ->label('HEX (для цвета)')
                            ->helperText('Напр. #000000')
                            ->required(fn () => Attribute::find($get('attribute_id'))?->code === 'color'),
                        TextInput::make('sort')->numeric()->default(1),
                    ];
                })
                // Сохранение новой опции (ВОТ ЭТОГО РАНЬШЕ НЕ ХВАТАЛО)
                ->createOptionUsing(function (array $data) {
                    // safety: если цвет — нормализуем HEX (# в начале)
                    if (!empty($data['slug'])) {
                        $hex = trim($data['slug']);
                        if ($hex && $hex[0] !== '#') $hex = "#{$hex}";
                        $data['slug'] = $hex;
                    }

                    // attribute_id мы передаём через Hidden
                    $value = AttributeValue::create([
                        'attribute_id' => $data['attribute_id'],
                        'value'        => $data['value'],
                        'slug'         => $data['slug'] ?? null, // для color — HEX
                        'sort'         => $data['sort'] ?? 1,
                    ]);

                    return $value->id; // вернуть ID новой опции в селект
                }),

            // Поле из PAV для ручного значения (если не выбрано из списка)
            TextInput::make('value_text')
                ->label('Произвольное значение')
                ->helperText('Если не выбрано «Значение (из списка)».'),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('attribute.name')
            ->columns([
                TextColumn::make('attribute.name')->label('Атрибут')->searchable(),
                TextColumn::make('attributeValue.value')->label('Значение (список)')->toggleable(),
                TextColumn::make('value')->label('Значение (ручное)')->toggleable(),
                TextColumn::make('updated_at')->label('Обновлено')->dateTime('d.m.Y H:i'),
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
