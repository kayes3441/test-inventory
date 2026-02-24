<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
    protected static bool $canCreateAnother = false;
    protected function getCreatedNotificationTitle():string
    {
        return 'Product successfully created.';
    }
}
