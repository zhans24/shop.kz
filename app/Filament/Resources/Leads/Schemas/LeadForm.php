<?php

namespace App\Filament\Resources\Leads\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основное')->schema([
                    TextInput::make('name')->label('Имя клиента'),
                    TextInput::make('phone')->label('Телефон'),
                    TextInput::make('email')->label('Email')->email(),
                    Select::make('status')
                        ->label('Статус')
                        ->options([
                            'new' => 'Новый',
                            'in_progress' => 'В работе',
                            'done' => 'Завершён',
                        ])
                        ->default('new'),
                    Textarea::make('message')->label('Комментарий')->rows(4),
                ])->columns(2)->columnSpanFull(),
            ]);
    }
}
