<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Optional extends Model
{
    protected $fillable = [
        'name',
        'vehicle_id',
        'optionals_id'
    ];

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class,
            'vehicle_optionals',
            'optionals_id',
            'vehicle_id');
    }
}
