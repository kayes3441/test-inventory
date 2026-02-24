<?php

namespace App\Filament\Resources\Products\Schemas;


use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                
                                TextInput::make('sku')
                                    ->label('SKU')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->columnSpan(1),
                            ]),
                        
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Pricing')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('purchase_price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('৳')
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->columnSpan(1),
                                
                                TextInput::make('sell_price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('৳')
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make('Inventory')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('opening_stock')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->columnSpan(1),
                                
                                TextInput::make('current_stock')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->columnSpan(1),
                            ]),
                    ]),
            ]);
    }
}
