<?php

namespace App\Filament\Resources\JournalEntries\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class JournalEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Entry #')
                    ->sortable(),
                
                TextColumn::make('entry_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                
                TextColumn::make('reference_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sale' => 'success',
                        'purchase' => 'warning',
                        'opening_stock' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->searchable(),
                
                TextColumn::make('totalDebit')
                    ->label('Total Debit')
                    ->money('BDT')
                    ->getStateUsing(fn ($record) => $record->lines()->sum('debit')),
                
                TextColumn::make('totalCredit')
                    ->label('Total Credit')
                    ->money('BDT')
                    ->getStateUsing(fn ($record) => $record->lines()->sum('credit')),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('reference_type')
                    ->options([
                        'sale' => 'Sale',
                        'purchase' => 'Purchase',
                        'opening_stock' => 'Opening Stock',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('entry_date', 'desc');
    }
}
