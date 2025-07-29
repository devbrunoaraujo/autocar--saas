<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'vehicle_id',
        'status'
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


    //Relationships
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }


}
