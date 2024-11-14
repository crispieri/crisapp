<?php

namespace App\Filament\Clusters\Resources\BrandResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Clusters\Resources\BrandResource;

class ListBrands extends ListRecords
{
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
