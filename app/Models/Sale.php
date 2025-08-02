<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'vehicle_id',
        'user_id',
        'customer_id',
        'sale_date',
        'amount',
        'payment_method',
    ];

    protected $casts = [
        'sale_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Customer::class);
    }
}
