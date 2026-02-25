<?php

namespace App\Filament\Resources\JournalEntries\Pages;

use App\Filament\Resources\JournalEntries\JournalEntryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditJournalEntry extends EditRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
