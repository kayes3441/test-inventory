<?php

namespace App\Filament\Resources\Sales\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                
                TextColumn::make('customer_name')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Walk-in Customer'),
                
                TextColumn::make('customer_phone')
                    ->searchable()
                    ->toggleable(),
                
                TextColumn::make('sale_date')
                    ->date()
                    ->sortable(),
                
                TextColumn::make('total_amount')
                    ->money('USD')
                    ->sortable(),
                
                TextColumn::make('paid_amount')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('due_amount')
                    ->money('USD')
                    ->sortable()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
                
                TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'partial' => 'warning',
                        'due' => 'danger',
                    })
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('payment_status')
                    ->options([
                        'paid' => 'Paid',
                        'partial' => 'Partial',
                        'due' => 'Due',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
          
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sale_date', 'desc');
    }
}
