<?php

namespace App\Filament\Clusters\Resources\CategoryResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Clusters\Resources\CategoryResource;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}
