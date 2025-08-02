<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'document',
        'document_type',
        'birth_date',
        'is_active',
        'is_verified',
        'verified_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'birth_date' => 'date',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
