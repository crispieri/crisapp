<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Pages\SubNavigationPosition;

class ManageProduct extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    public static function getModelLabel(): string
    {
        return __('order.modelLabel');
    }

    public static function getPluralModelLabel(): string
    {
        return __('order.pluralModelLabel');
    }

    public static function getClusterBreadcrumb(): string
    {
        return __('product.manageProduct');
    }
}
