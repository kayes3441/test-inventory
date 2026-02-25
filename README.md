# Inventory & Accounting System ->  

A Laravel-based inventory and accounting system built with Filament 5.0.

## Features

- Product Management with inventory tracking
- Point of Sale (POS) interface for creating sales
- Automatic journal entry creation for sales
- Chart of Accounts management
- Journal Entries with debit/credit validation
- Organized navigation with groups (Inventory, Sales, Accounting)

## Installation

1. Clone the repository
2. Install dependencies:
```bash
composer install
3. Configure your environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Run migrations and seed the database:

This will create the necessary tables and seed the basic chart of accounts (CASH, SALES, COGS, INVENTORY).


6. Start the development server:
```bash
php artisan serve
```

## Usage

### Chart of Accounts

The system comes with 4 basic accounts:
- CASH (Asset)
- INVENTORY (Asset)
- SALES (Revenue)
- COGS (Cost of Goods Sold)

You can add more accounts through the Accounts resource in the Accounting navigation group.

### Creating Products

1. Navigate to Products in the Inventory group
2. Click "New Product"
3. Fill in product details including purchase price and selling price
4. Set initial stock quantity

### Creating Sales

1. Navigate to Sales in the Sales group
2. Click "New Sale"
3. Add products to the sale using the repeater
4. The system automatically calculates:
   - Item subtotals
   - Total subtotal
   - Discount amount
   - VAT amount
   - Total amount
   - Due amount (based on paid amount)
5. Invoice number is auto-generated (format: INV-YYYYMMDD-0001)
6. Upon saving, a journal entry is automatically created with:
   - Debit Cash (total amount)
   - Credit Sales Revenue (total amount)
   - Debit COGS (cost of goods sold)
   - Credit Inventory (cost of goods sold)

### Journal Entries

Journal entries are automatically created when sales are made, but you can also create manual entries:

1. Navigate to Journal Entries in the Accounting group
2. Click "New Journal Entry"
3. Add journal entry lines (debits and credits)
4. The system validates that total debits equal total credits
5. View totals at the bottom of the form

## Technical Details

- Built with Laravel 11
- Filament 5.0 for admin panel
- UUID primary keys for all models
- Soft deletes enabled
- Reactive form calculations using Filament's live() method
- Automatic journal entry creation via AccountingService

## Currency

The system uses Bangladeshi Taka (৳) as the currency symbol.

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
