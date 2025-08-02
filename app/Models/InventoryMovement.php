<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryMovement extends Model
{

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'movement_type',
        'origin',
        'movement_date',
        'purchase_price',
        'description',
    ];

    protected $casts = [
        'movement_date' => 'datetime',
        'purchase_price' => 'float',
    ];
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
