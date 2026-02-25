<?php

namespace App\Filament\Resources\JournalEntries\Schemas;

use App\Models\Account;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class JournalEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Journal Entry Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('entry_date')
                                    ->label('Entry Date')
                                    ->required()
                                    ->default(now())
                                    ->native(false)
                                    ->columnSpan(1),
                                
                                Select::make('reference_type')
                                    ->label('Reference Type')
                                    ->options([
                                        'sale' => 'Sale',
                                        'purchase' => 'Purchase',
                                        'opening_stock' => 'Opening Stock',
                                    ])
                                    ->required()
                                    ->columnSpan(1),
                            ]),
                        
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Journal Entry Lines')
                    ->description('Debits must equal Credits')
                    ->schema([
                        Repeater::make('lines')
                            ->relationship()
                            ->schema([
                                Select::make('account_id')
                                    ->label('Account')
                                    ->options(Account::where('is_active', true)->pluck('account_name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->columnSpan(2),
                                
                                TextInput::make('debit')
                                    ->label('Debit')
                                    ->numeric()
                                    ->prefix('৳')
                                    ->default(0)
                                    ->minValue(0)
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::updateTotals($get, $set);
                                    })
                                    ->columnSpan(1),
                                
                                TextInput::make('credit')
                                    ->label('Credit')
                                    ->numeric()
                                    ->prefix('৳')
                                    ->default(0)
                                    ->minValue(0)
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::updateTotals($get, $set);
                                    })
                                    ->columnSpan(1),
                            ])
                            ->columns(4)
                            ->defaultItems(2)
                            ->addActionLabel('Add Line')
                            ->collapsible()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            })
                            ->itemLabel(fn (array $state): ?string => 
                                isset($state['account_id']) ? Account::find($state['account_id'])?->account_name : 'New Line'
                            ),
                        
                        Grid::make(3)
                            ->schema([
                                TextInput::make('total_debit')
                                    ->label('Total Debit')
                                    ->prefix('৳')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->default(0),
                                
                                TextInput::make('total_credit')
                                    ->label('Total Credit')
                                    ->prefix('৳')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->default(0),
                                
                                TextInput::make('difference')
                                    ->label('Difference')
                                    ->prefix('৳')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->default(0)
                                    ->helperText('Must be 0.00 for balanced entry'),
                            ]),
                    ]),
            ]);
    }

    protected static function updateTotals(Get $get, Set $set): void
    {
        $lines = $get('../../lines') ?? [];
        
        $totalDebit = 0;
        $totalCredit = 0;
        
        foreach ($lines as $line) {
            $totalDebit += floatval($line['debit'] ?? 0);
            $totalCredit += floatval($line['credit'] ?? 0);
        }
        
        $difference = $totalDebit - $totalCredit;
        
        $set('../../total_debit', number_format($totalDebit, 2, '.', ''));
        $set('../../total_credit', number_format($totalCredit, 2, '.', ''));
        $set('../../difference', number_format($difference, 2, '.', ''));
    }
}
