<?php

namespace App\Filament\Resources\JournalEntries\Pages;

use App\Filament\Resources\JournalEntries\JournalEntryResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateJournalEntry extends CreateRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $totalDebit = collect($data['lines'] ?? [])->sum('debit');
        $totalCredit = collect($data['lines'] ?? [])->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            Notification::make()
                ->danger()
                ->title('Journal Entry Not Balanced')
                ->body("Total Debits (৳{$totalDebit}) must equal Total Credits (৳{$totalCredit})")
                ->persistent()
                ->send();

            $this->halt();
        }

        return $data;
    }
}
