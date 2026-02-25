<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'entry_date',
        'reference_type',
        'reference_id',
        'description',
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];

    public function lines():HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function reference():MorphTo
    {
        return $this->morphTo();
    }

    public function getTotalDebitAttribute()
    {
        return $this->lines()->sum('debit');
    }

    public function getTotalCreditAttribute()
    {
        return $this->lines()->sum('credit');
    }

    public function isBalanced():float
    {
        return round($this->totalDebit, 2) === round($this->totalCredit, 2);
    }
}
