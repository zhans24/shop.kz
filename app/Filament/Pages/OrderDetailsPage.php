<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class OrderDetailsPage extends Page
{
    protected string $view = 'filament.pages.order-details-page';
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string|null|\UnitEnum $navigationGroup = 'Продажи';
    protected static ?string $navigationLabel = 'Методы доставки и оплаты';
    protected static ?string $title = 'Методы доставки и оплаты';
    protected static ?int $navigationSort=22;

}
