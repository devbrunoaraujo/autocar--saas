<?php

namespace App\Models;

use App\Contracts\Image\ImageProcessorInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Model para representar um veículo
 *
 * Este model armazena as informações dos veículos consultados na API da FIPE
 */
class Vehicle extends Model
{
    use HasFactory;

    /**
     * Campos que podem ser preenchidos em massa
     */
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

    /**
     * Conversões de tipo para os atributos
     */
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

        static::saving(function ($car) {
            if (! $car->is_active) {
                $car->is_featured = false;
            }
        });

        static::deleting(function ($vehicle) {
            if ($vehicle->thumbnail) {
                Storage::disk('public')->delete($vehicle->thumbnail);
            }
            if (is_array($vehicle->gallery)) {
                foreach ($vehicle as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
        });
    }
     /**
     * Scope para filtrar por tipo de veículo
     */
    public function scopeByVehicleType($query, $vehicleType)
    {
        return $query->where('vehicle_type', $vehicleType);
    }

    /**
     * Scope para filtrar por marca
     */
    public function scopeByBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    /**
     * Scope para filtrar por modelo
     */
    public function scopeByModel($query, $modelId)
    {
        return $query->where('model_id', $modelId);
    }

    /**
     * Accessor para formatar o preço FIPE
     */
    public function getFormattedFipePriceAttribute()
    {
        return $this->fipe_price ? "R$ {$this->fipe_price}" : null;
    }

    /**
     * Accessor para nome completo do veículo
     */
    public function getFullNameAttribute()
    {
        return "{$this->brand_name} {$this->model_name} {$this->year_name}";
    }
}
