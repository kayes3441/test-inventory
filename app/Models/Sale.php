<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'customer_name',
        'customer_phone',
        'sale_date',
        'subtotal',
        'discount_amount',
        'vat_percentage',
        'vat_amount',
        'total_amount',
        'paid_amount',
        'due_amount',
        'payment_status',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'vat_percentage' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
    ];

    public function saleItems():HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments():HasMany
    {
        return $this->hasMany(Payment::class);
    }

    protected static function boot():void
    {
        parent::boot();

        static::creating(function ($sale) {
            if (empty($sale->invoice_number)) {
                $sale->invoice_number = 'INV-' . date('Ymd') . '-' . str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
