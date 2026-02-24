<?php

namespace App\Filament\Resources\Sales\Pages;

use App\Filament\Resources\Sales\SaleResource;
use App\Models\Sale;
use Filament\Resources\Pages\CreateRecord;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;
    protected static bool $canCreateAnother = false;
    protected function getCreatedNotificationTitle():string
    {
        return 'Sale successfully generated.';
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['invoice_number'] = 'INV-' . date('Ymd') . '-' . str_pad(
            Sale::whereDate('created_at', today())->count() + 1,
            4,
            '0',
            STR_PAD_LEFT
        );

        $data['sale_date'] = now();
        return $data;
    }
}
