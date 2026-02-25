<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Sale;

class AccountingService
{
    /**
     * Create journal entry for a sale
     */
    public static function recordSale(Sale $sale): void
    {
        $cashAccount = Account::where('account_code', 'CASH')->first();
        $salesRevenueAccount = Account::where('account_code', 'SALES')->first();
        $cogsAccount = Account::where('account_code', 'COGS')->first();
        $inventoryAccount = Account::where('account_code', 'INVENTORY')->first();

        if (!$cashAccount || !$salesRevenueAccount || !$cogsAccount || !$inventoryAccount) {
            return;
        }
        $totalCost = 0;
        foreach ($sale->saleItems as $item) {
            $totalCost += $item->product->purchase_price * $item->quantity;
        }
        $journalEntry = JournalEntry::create([
            'entry_date' => $sale->sale_date,
            'reference_type' => 'sale',
            'reference_id' => $sale->id,
            'description' => "Sale - Invoice: {$sale->invoice_number}",
        ]);

        $journalEntry->lines()->create([
            'account_id' => $cashAccount->id,
            'debit' => $sale->total_amount,
            'credit' => 0,
        ]);

        $journalEntry->lines()->create([
            'account_id' => $salesRevenueAccount->id,
            'debit' => 0,
            'credit' => $sale->total_amount,
        ]);

        $journalEntry->lines()->create([
            'account_id' => $cogsAccount->id,
            'debit' => $totalCost,
            'credit' => 0,
        ]);
        $journalEntry->lines()->create([
            'account_id' => $inventoryAccount->id,
            'debit' => 0,
            'credit' => $totalCost,
        ]);
    }
}
