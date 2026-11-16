<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Filament\Resources\OrderResource;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverview extends BaseWidget
{
    public static string $resource = OrderResource::class;

    protected function getCards(): array
    {
        return [
            Card::make('Total Orders', \App\Models\Order::count())
                ->description('Number of orders placed')
                ->descriptionIcon('heroicon-o-shopping-cart'),
            Card::make('Total Revenue', \App\Models\Order::sum('total_amount'))
                ->description('Total revenue from orders')
                ->descriptionIcon('heroicon-o-currency-dollar'),
            // Add more cards as needed
        ];
    }
}