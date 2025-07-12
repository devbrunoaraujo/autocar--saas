<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_type',
        'brand_id',
        'brand_name',
        'model_id',
        'model_name',
        'year_id',
        'year_name',
        'fuel',
        'fuel_acronym',
        'fipe_price',
        'month_reference',
        'purchase_price',
        'sale_price',
        'color',
        'transmission',
        'mileage',
        'renavam',
        'crv',
        'chassis_number',
        'license_plate',
        'notes',
        'is_active',
        'is_featured',
        'thumbnail',
        'gallery',
    ];

    protected $casts = [
        'brand_id' => 'integer',
        'model_id' => 'integer',
        'purchase_price' => 'float',
        'sale_price' => 'float',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'gallery' => 'array',
    ];

    protected static function booted()
    {
        static::saving(function ($vehicle) {
            if (!$vehicle->is_active) {
                $vehicle->is_featured = false;
            }
        });

        static::deleting(function ($vehicle) {
            // Deleta thumbnail se existir
            if (!empty($vehicle->thumbnail)) {
                Storage::disk('public')->delete($vehicle->thumbnail);
            }
            // Deleta imagens da galeria se existir
            if (!empty($vehicle->gallery)) {
                $gallery = is_array($vehicle->gallery) ? $vehicle->gallery : json_decode($vehicle->gallery, true);
                if (is_array($gallery)) {
                    foreach ($gallery as $image) {
                        if (!empty($image)) {
                            Storage::disk('public')->delete($image);
                        }
                    }
                }
            }
        });
    }

    // Scopes
    public function scopeByVehicleType($query, $vehicleType)
    {
        return $query->where('vehicle_type', $vehicleType);
    }

    public function scopeByBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    public function scopeByModel($query, $modelId)
    {
        return $query->where('model_id', $modelId);
    }

    // Accessors
    public function getFormattedFipePriceAttribute()
    {
        return $this->fipe_price ? "R$ {$this->fipe_price}" : null;
    }

    public function getFullNameAttribute()
    {
        return "{$this->brand_name} {$this->model_name} {$this->year_name}";
    }

    // Relationships
    public function optionals(): BelongsToMany
    {
        return $this->belongsToMany(
            Optional::class,
            'vehicle_optionals',
            'vehicle_id',
            'optionals_id'
        );
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }
}
