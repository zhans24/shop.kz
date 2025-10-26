<?php

namespace App\Filament\Resources\Collections\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CollectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Select::make('type')
                    ->options(['manual' => 'Manual', 'rule' => 'Rule'])
                    ->default('manual')
                    ->required(),
                TextInput::make('rule'),
            ]);
    }
}
