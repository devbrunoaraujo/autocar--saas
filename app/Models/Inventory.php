<?php

namespace App\Models;

use Carbon\Carbon;
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

    /**
     * Calcula o número de dias que o veículo ficou/está no estoque.
     */
    public function calculateDaysInStock(): ?int
    {
        if (!$this->entry_date) {
            return null;
        }
        $entry = Carbon::parse($this->entry_date);
        $exit = $this->exit_date ? Carbon::parse($this->exit_date) : now();

        return $entry->diffInDays($exit);
    }

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
