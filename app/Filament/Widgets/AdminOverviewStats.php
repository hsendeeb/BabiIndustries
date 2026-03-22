<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Industries\IndustryResource;
use App\Models\Category;
use App\Models\Industry;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminOverviewStats extends StatsOverviewWidget
{
    protected ?string $heading = 'Overview';

    protected function getStats(): array
    {
        return [
            Stat::make('Industries', Industry::query()->count())
                ->description('View all industries')
                ->icon(Heroicon::OutlinedBuildingOffice2)
                ->color('primary')
                ->url(IndustryResource::getUrl('index')),
            Stat::make('Categories', Category::query()->count())
                ->description('View all categories')
                ->icon(Heroicon::OutlinedTag)
                ->color('success')
                ->url(CategoryResource::getUrl('index')),
        ];
    }
}
