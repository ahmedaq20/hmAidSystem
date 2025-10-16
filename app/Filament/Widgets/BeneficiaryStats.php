<?php

namespace App\Filament\Widgets;

use App\Models\Beneficiary;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BeneficiaryStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
            return [
                Stat::make('عدد المستفيدين الكلي', Beneficiary::count())
                ->description('إجمالي عدد المستفيدين المسجلين')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('عدد الشهداء', Beneficiary::sum('martyrs_count'))
                ->description('إجمالي عدد الشهداء بين المستفيدين')
                ->descriptionIcon('heroicon-m-heart')
                ->color('danger'),

            Stat::make('عدد الجرحى', Beneficiary::sum('injured_count'))
                ->description('إجمالي عدد الجرحى المسجلين في النظام')
                ->descriptionIcon('heroicon-m-plus')
                ->color('warning'),

            Stat::make('عدد ذوي الإعاقة', Beneficiary::sum('disabled_count'))
                ->description('إجمالي عدد ذوي الإعاقة في النظام')
                ->descriptionIcon('heroicon-m-no-symbol')
                ->color('info'),
        ];
    }
}