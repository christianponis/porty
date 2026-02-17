<?php

namespace App\Models;

use App\Enums\NodoTransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NodiTransaction extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'wallet_id',
        'booking_id',
        'type',
        'amount',
        'balance_after',
        'description',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'type' => NodoTransactionType::class,
            'amount' => 'decimal:2',
            'balance_after' => 'decimal:2',
            'metadata' => 'array',
        ];
    }

    // --- Relazioni ---

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(NodiWallet::class, 'wallet_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
