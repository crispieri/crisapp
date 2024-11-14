<?php

namespace App\Filament\Clusters\Resources\BrandResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Clusters\Resources\BrandResource;

class CreateBrand extends CreateRecord
{
    protected static string $resource = BrandResource::class;
}
