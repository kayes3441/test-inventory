<?php

namespace App\Filament\Resources\Accounts\Schemas;

use App\Models\Account;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Account Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('account_code')
                                    ->label('Account Code')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->placeholder('e.g., CASH, SALES, COGS, INVENTORY')
                                    ->datalist([
                                        'CASH',
                                        'BANK',
                                        'INVENTORY',
                                        'AR',
                                        'AP',
                                        'SALES',
                                        'COGS',
                                        'EXPENSE',
                                        'REVENUE',
                                        'EQUITY',
                                        'LIABILITY',
                                        'RENT',
                                        'UTILITIES',
                                        'SALARY',
                                    ])
                                    ->helperText('Type your own code or select from suggestions')
                                    ->autocomplete(false),
                                
                                TextInput::make('account_name')
                                    ->label('Account Name')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('account_type')
                                    ->label('Account Type')
                                    ->options([
                                        'asset' => 'Asset',
                                        'liability' => 'Liability',
                                        'equity' => 'Equity',
                                        'revenue' => 'Revenue',
                                        'expense' => 'Expense',
                                        'cogs' => 'Cost of Goods Sold',
                                    ])
                                    ->required(),

                                Select::make('parent_id')
                                    ->label('Parent Account')
                                    ->options(Account::query()->pluck('account_name', 'id'))
                                    ->searchable()
                                    ->nullable(),
                            ]),
                        
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),
            ]);
    }
}
