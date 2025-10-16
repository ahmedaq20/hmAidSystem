<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use App\Filament\Widgets\BeneficiaryStats;

class Dashboard extends Page
{
    protected string $view = 'filament.pages.dashboard';
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $title = 'لوحة التحكم'; // بالعربية لو أردت
    
    // protected function getWidgets(): array
    // {
    //     return [
    //         BeneficiaryStats::class,
    //     ];
    // }
}