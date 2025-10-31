<?php

namespace App\Filament\Pages;

use App\Models\ContactSetting;
use App\Services\ContactNormalizer;
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

    protected string $view = 'filament.pages.contact-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $s = ContactSetting::singleton();

        $phones = collect($s->phones ?? [])
            ->take(2)
            ->map(function ($row) {
                $raw = is_array($row) ? ($row['raw'] ?? null) : (string)$row;
                return [
                    'raw' => $raw,
                    'tel' => ContactNormalizer::telHrefFromRaw($raw),
                ];
            })->values()->all();

        $this->data = [
            'company_name' => $s->company_name,
            'company_text' => $s->company_text,

            'phones'       => $phones,
            'email'        => $s->email,

            'whatsapp'     => $s->whatsapp,
            'facebook'     => $s->facebook,
            'tiktok'       => $s->tiktok,
            'youtube'      => $s->youtube,
            'telegram'     => $s->telegram,

            'address'      => $s->address,
            'map_embed'    => $s->map_embed,
        ];
    }

    public function schema(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
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
                            ->label('Телефоны (макс. 2)')
                            ->schema([
                                TextInput::make('raw')
                                    ->label('Номер')
                                    ->placeholder('+7 777 000 00 00')
                                    ->helperText('Формат: +7 777 000 00 00')
                                    ->regex('/^\+?\d[\d\s\-\(\)]{7,}$/')
                                    ->validationAttribute('номер телефона')
                                    ->maxLength(30)
                                    ->extraInputAttributes(['x-mask' => '+7 999 999 99 99'])
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set) {
                                        $set('tel', ContactNormalizer::telHrefFromRaw($state));
                                    }),
                            ])
                            ->addActionLabel('Добавить телефон')
                            ->minItems(0)
                            ->maxItems(2)
                            ->dehydrateStateUsing(function ($state) {
                                return collect($state ?? [])
                                    ->take(2)
                                    ->map(function ($row) {
                                        $raw = trim((string) data_get($row, 'raw'));
                                        if ($raw === '') return null;
                                        return [
                                            'raw' => $raw,
                                            'tel' => ContactNormalizer::telHrefFromRaw($raw),
                                        ];
                                    })
                                    ->filter()
                                    ->values()
                                    ->all();
                            })
                            ->columnSpanFull(),

                        TextInput::make('email')->label('Email')->email()->maxLength(255),
                    ])
                    ->columns(1)
                    ->columnSpan(6),

                Section::make('Соцсети')
                    ->schema([
                        TextInput::make('whatsapp')
                            ->label('WhatsApp (вводи номер)')
                            ->placeholder('+7 777 000 00 00')
                            ->helperText('Сохраним как ссылку вида https://wa.me/...')
                            ->extraInputAttributes(['x-mask' => '+7 999 999 99 99'])
                            ->maxLength(255),

                        TextInput::make('facebook')
                            ->label('Facebook (@handle или ссылка)')
                            ->placeholder('@brand или https://facebook.com/brand')
                            ->maxLength(255),

                        TextInput::make('tiktok')
                            ->label('TikTok (@handle или ссылка)')
                            ->placeholder('@brand или https://www.tiktok.com/@brand')
                            ->maxLength(255),

                        TextInput::make('youtube')
                            ->label('YouTube (@handle или ссылка)')
                            ->placeholder('@brand или https://youtube.com/@brand')
                            ->maxLength(255),

                        TextInput::make('telegram')
                            ->label('Telegram (@handle или ссылка)')
                            ->placeholder('@brand или https://t.me/brand')
                            ->maxLength(255),

                    ])
                    ->columns(1)
                    ->columnSpan(6),

                Section::make('Адрес и карта')
                    ->schema([
                        Textarea::make('address')->label('Адрес')->rows(2),
                        Textarea::make('map_embed')->label('Карта (iframe или URL)')->rows(3)
                            ->helperText('Вставьте HTML iframe или прямую ссылку.'),
                    ])
                    ->columns(1)
                    ->columnSpan(6),

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

                        $payload['phones'] = collect($payload['phones'] ?? [])->take(2)->values()->all();

                        ContactSetting::singleton()->fill($payload)->save();

                        cache()->forget('site.contacts');

                        Notification::make()->title('Контакты сохранены')->success()->send();
                    }),
            ])
            ->columns(12);
    }
}
