<?php

namespace App\Filament\Resources\Sales\Schemas;

use App\Models\Product;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;


class SaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Sale Products')
                    ->schema([
                        Repeater::make('saleItems')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->options(Product::query()->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        if (! $state) {
                                            $set('unit_price', 0);
                                            $set('quantity', 1);
                                            $set('subtotal', 0);
                                            self::updateParentTotals($get, $set);
                                            return;
                                        }

                                        $product = Product::find($state);
                                        if ($product) {
                                            $set('unit_price', $product->sell_price);
                                            $set('quantity', 1);

                                            $qty   = 1;
                                            $price = $product->sell_price;
                                            $set('subtotal', round($qty * $price, 2));
                                        }

                                        self::updateParentTotals($get, $set);
                                    })
                                    ->columnSpan(3),

                                TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $qty   = floatval($get('quantity') ?? 1);
                                        $price = floatval($get('unit_price') ?? 0);
                                        $set('subtotal', round($qty * $price, 2));

                                        self::updateParentTotals($get, $set);
                                    })
                                    ->columnSpan(2),

                                TextInput::make('unit_price')
                                    ->label('Unit Price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('৳')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $qty   = floatval($get('quantity') ?? 1);
                                        $price = floatval($get('unit_price') ?? 0);
                                        $set('subtotal', round($qty * $price, 2));

                                        self::updateParentTotals($get, $set);
                                    })
                                    ->columnSpan(2),

                                TextInput::make('subtotal')
                                    ->required()
                                    ->numeric()
                                    ->prefix('৳')
                                    ->disabled()
                                    ->dehydrated(true)
                                    ->columnSpan(2),
                            ])
                            ->columns(9)
                            ->defaultItems(0)
                            ->addActionLabel('Add Product')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                            isset($state['product_id'])
                                ? Product::find($state['product_id'])?->name
                                : 'New Item'
                            ),
                    ])
                    ->columnSpan(2),

                Section::make('Sale Details')
                    ->extraAttributes(['style' => 'max-height: calc(100vh - 12rem); overflow-y: auto;'])
                    ->schema([
                        Select::make('payment_status')
                            ->label('Payment Status')
                            ->options([
                                'paid'    => 'Paid',
                                'partial' => 'Partial',
                                'due'     => 'Due',
                            ])
                            ->default('due')
                            ->required(),

                        TextInput::make('customer_name')
                            ->label('Customer Name')
                            ->maxLength(255),

                        TextInput::make('customer_phone')
                            ->label('Customer Phone')
                            ->tel()
                            ->maxLength(255),
                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->required()
                            ->numeric()
                            ->prefix('৳')
                            ->disabled()
                            ->dehydrated(true)
                            ->live()
                            ->afterStateHydrated(fn (Get $get, Set $set) => self::updateParentTotals($get, $set)),

                        TextInput::make('discount_amount')
                            ->label('Discount Amount')
                            ->numeric()
                            ->prefix('৳')
                            ->default(0)
                            ->minValue(0)
                            ->dehydrated(true)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::calculateTotal($get, $set);
                            }),

                        TextInput::make('vat_percentage')
                            ->label('VAT %')
                            ->numeric()
                            ->suffix('%')
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(100)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::calculateTotal($get, $set);
                            }),

                        TextInput::make('vat_amount')
                            ->label('VAT Amount')
                            ->required()
                            ->numeric()
                            ->prefix('৳')
                            ->disabled()
                            ->dehydrated(true),

                        TextInput::make('total_amount')
                            ->label('Total Amount')
                            ->required()
                            ->numeric()
                            ->prefix('৳')
                            ->disabled()
                            ->dehydrated(true),

                        TextInput::make('paid_amount')
                            ->label('Paid Amount')
                            ->required()
                            ->numeric()
                            ->prefix('৳')
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(fn (Get $get) => floatval($get('total_amount') ?? 0))
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $total = floatval($get('total_amount') ?? 0);
                                $paid  = floatval($get('paid_amount') ?? 0);
                                $set('due_amount', round(max(0, $total - $paid), 2));
                            })
                            ->helperText(fn (Get $get) => 'Maximum: ৳' . number_format($get('total_amount') ?? 0, 2)),

                        TextInput::make('due_amount')
                            ->label('Due Amount')
                            ->required()
                            ->numeric()
                            ->prefix('৳')
                            ->disabled()
                            ->dehydrated(true),
                    ])
                    ->columnSpan(1),
            ]);
    }

    protected static function updateParentTotals(Get $get, Set $set): void
    {
        $items = $get('../../saleItems') ?? [];

        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += floatval($item['subtotal'] ?? 0);
        }

        $set('../../subtotal', round($subtotal, 2));


        $discount    = floatval($get('../../discount_amount') ?? 0);
        $vatPercent  = floatval($get('../../vat_percentage') ?? 0);

        $afterDiscount = round($subtotal, 2) - $discount;
        $vatAmount     = $afterDiscount * ($vatPercent / 100);
        $total         = $afterDiscount + $vatAmount;

        $set('../../vat_amount',   round($vatAmount, 2));
        $set('../../total_amount', round($total, 2));

        $paid = floatval($get('../../paid_amount') ?? 0);
        $set('../../due_amount',   round(max(0, $total - $paid), 2));
    }

    protected static function calculateTotal(Get $get, Set $set): void
    {
        $subtotal    = floatval($get('subtotal') ?? 0);
        $discount    = floatval($get('discount_amount') ?? 0);
        $vatPercent  = floatval($get('vat_percentage') ?? 0);

        $afterDiscount = $subtotal - $discount;
        $vatAmount     = $afterDiscount * ($vatPercent / 100);
        $total         = $afterDiscount + $vatAmount;

        $set('vat_amount',   round($vatAmount, 2));
        $set('total_amount', round($total, 2));

        $paid = floatval($get('paid_amount') ?? 0);
        $set('due_amount',   round(max(0, $total - $paid), 2));
    }
}