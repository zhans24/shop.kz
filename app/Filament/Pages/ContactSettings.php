<?php

namespace App\Filament\Pages;

use App\Models\ContactSetting;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;

class ContactSettings extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-phone';
    protected static ?string $navigationLabel = 'Контакты';
    protected static ?string $title           = 'Контакты сайта';
    protected static ?string $slug            = 'contact-settings';
    protected static string|null|\UnitEnum $navigationGroup = 'Управление';
    protected static ?int    $navigationSort  = 10;

    // кастомный blade, чтобы ограничить ширину контейнера
    protected string $view = 'filament.pages.contact-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $s = ContactSetting::singleton();

        $this->data = [
            'company_name' => $s->company_name,
            'company_text' => $s->company_text,

            'phones'       => $s->phones ?: [],
            'email'        => $s->email,

            'whatsapp'     => $s->whatsapp,
            'tiktok'       => $s->tiktok,
            'instagram'    => $s->instagram,
            'youtube'      => $s->youtube,

            'address'      => $s->address,
            'map_embed'    => $s->map_embed,
        ];
    }

    public function schema(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                // левая колонка
                Section::make('Компания')
                    ->schema([
                        TextInput::make('company_name')->label('Название компании')->maxLength(255),
                        Textarea::make('company_text')->label('Текст о компании')->rows(3),
                    ])
                    ->columns(1)
                    ->columnSpan(6),

                Section::make('Контакты')
                    ->schema([
                        Repeater::make('phones')
                            ->label('Телефоны')
                            ->schema([
                                TextInput::make('label')
                                    ->label('Метка')
                                    ->placeholder('Основной / WhatsApp / Офис')
                                    ->maxLength(50),

                                TextInput::make('number')
                                    ->label('Номер')
                                    ->placeholder('+7 777 000 00 00')
                                    ->regex('/^\+?[0-9\-\s\(\)]+$/')
                                    ->validationAttribute('номер телефона'),
                            ])
                            ->addActionLabel('Добавить телефон')
                            ->columns(2)
                            ->columnSpanFull(),

                        TextInput::make('email')->label('Email')->email()->maxLength(255),
                    ])
                    ->columns(1)
                    ->columnSpan(6),

                // правая колонка
                Section::make('Соцсети')
                    ->schema([
                        TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->placeholder('+7 777 000 00 00 или ссылка')
                            ->rule('nullable|url'),

                        TextInput::make('instagram')
                            ->label('Instagram')
                            ->placeholder('@brand или https://instagram.com/brand')
                            ->rule('nullable|url'),

                        TextInput::make('tiktok')
                            ->label('TikTok')
                            ->placeholder('@brand или https://www.tiktok.com/@brand')
                            ->rule('nullable|url'),

                        TextInput::make('youtube')
                            ->label('YouTube')
                            ->placeholder('@brand или https://youtube.com/@brand')
                            ->rule('nullable|url'),
                    ])
                    ->columns(1)
                    ->columnSpan(6),

                Section::make('Адрес и карта')
                    ->schema([
                        Textarea::make('address')->label('Адрес')->rows(2),

                        Textarea::make('map_embed')
                            ->label('Карта (iframe или URL)')
                            ->rows(3)
                            ->helperText('Вставьте HTML iframe или прямую ссылку.'),
                    ])
                    ->columns(1)
                    ->columnSpan(6),
            ])
            ->columns(12); // двухколоночная сетка
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Сохранить')
                ->icon('heroicon-o-check-circle')
                ->color('primary')
                ->action(function () {
                    $payload = collect($this->data ?? [])->map(function ($v) {
                        if (is_string($v)) {
                            $v = trim($v);
                            return $v === '' ? null : $v;
                        }
                        return $v === '' ? null : $v;
                    })->all();

                    $s = ContactSetting::singleton();
                    $s->fill($payload)->save();

                    Notification::make()->title('Контакты сохранены')->success()->send();
                }),
        ];
    }
}
