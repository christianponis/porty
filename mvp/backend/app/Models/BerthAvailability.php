<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BerthAvailability extends Model
{
    protected $connection = 'mysql';

    protected $table = 'berth_availabilities';

    protected $fillable = [
        'berth_id',
        'start_date',
        'end_date',
        'is_available',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_available' => 'boolean',
        ];
    }

    public function berth(): BelongsTo
    {
        return $this->belongsTo(Berth::class);
    }
}
