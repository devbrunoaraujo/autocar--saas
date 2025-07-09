<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    /**
     * Conversões de tipo para os atributos
     */
    protected $casts = [
        'brand_id' => 'integer',
        'model_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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
