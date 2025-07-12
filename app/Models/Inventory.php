<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $fillable = [
        'vehicle_id',
        'entry_date',
        'exit_date',
        'entry_type',
        'exit_type',
        'supplier_id',
        'total_cost',
    ];

    protected $casts = [
        'entry_date' => 'datetime',
        'exit_date' => 'datetime',
        'total_cost' => 'decimal:2',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
        **/
}
