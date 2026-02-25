<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'account_code',
        'account_name',
        'account_type',
        'parent_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function journalEntryLines():hasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function getBalanceAttribute()
    {
        $debits = $this->journalEntryLines()->sum('debit');
        $credits = $this->journalEntryLines()->sum('credit');
        if (in_array($this->account_type, ['asset', 'expense', 'cogs'])) {
            return $debits - $credits;
        }
        return $credits - $debits;
    }
}
